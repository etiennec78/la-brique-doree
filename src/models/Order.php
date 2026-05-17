<?php
require_once __DIR__ . '/../db_connect.php';

class Order {
    public static function getUserRunningOrder($uid) {
        global $pdo;
        $stmt = $pdo->prepare("
        SELECT o.id, os.id as status, o.cook_id, o.delivery_person_id, o.is_takeaway
        FROM order_status os
        JOIN orders o on o.order_status_id = os.id
        WHERE o.customer_id = ?
        ORDER BY o.id DESC
        LIMIT 1
        ");
        $stmt->execute([$uid]);
        return $stmt->fetch();
    }

    public static function getOrderById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function getOrdersFromState($order_names) {
        global $pdo;
        $placeholders = implode(',', array_fill(0, count($order_names), '?'));
        $stmt = $pdo->prepare("
            SELECT o.id, u.first_name, u.last_name, o.is_takeaway, o.takeaway_time
            FROM orders o
            JOIN users u ON o.customer_id = u.id
            JOIN order_status os ON o.order_status_id = os.id
            WHERE os.name IN ($placeholders)
            ORDER BY COALESCE(o.takeaway_time, '1000-01-01 00:00:00') ASC, o.id ASC
        ");
        $stmt->execute($order_names);
        return $stmt->fetchAll();
    }

    public static function getAllOrdersFromUser($uid) {
        global $pdo;
        $stmt = $pdo->prepare("
        SELECT o.id, o.takeaway_time, o.cook_id, o.delivery_person_id, p.amount
        FROM orders o
        LEFT JOIN payment p ON o.cart_id = p.cart_id
        WHERE o.customer_id = ?
        ORDER BY o.id DESC
        ");
        $stmt->execute([$uid]);
        return $stmt->fetchAll();
    }

    public static function getAllCompletedOrderIdsFromUser($uid) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT o.id
            FROM orders o
            WHERE o.customer_id = ? AND o.order_status_id = 5
            ORDER BY o.id DESC
        ");
        $stmt->execute([$uid]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function getAvailableStaff($role_name) {
        global $pdo;

        $allowed_roles = ['cook', 'delivery_person'];
        if (!in_array($role_name, $allowed_roles, true)) {
            return null;
        }

        // Return false if the staff member has an order with this status
        $busy_status_map = [
            'cook' => 2, // 2 = preparing
            'delivery_person' => 4 // 4 = shipping
        ];
        $busy_status_id = $busy_status_map[$role_name];

        // Get the staff member assigned a long time ago, that is currently not busy
        $stmt = $pdo->prepare("
            SELECT u.id
            FROM users u
            JOIN role r ON u.role_id = r.id
            LEFT JOIN orders o ON o.{$role_name}_id = u.id
            WHERE r.name = ?
            AND NOT EXISTS (
                SELECT 1
                FROM orders active_o
                WHERE active_o.{$role_name}_id = u.id
                AND active_o.order_status_id = ?
            )
            GROUP BY u.id
            ORDER BY MAX(o.{$role_name}_assigned_at) ASC
            LIMIT 1
        ");
        $stmt->execute([$role_name, $busy_status_id]);

        $staff_id = $stmt->fetch(PDO::FETCH_COLUMN);
        return $staff_id !== false ? $staff_id : null; // Return null if false
    }

    public static function setShippingStatus($order_id, $delivery_person_id) {
        global $pdo;
        $stmt = $pdo->prepare("
            UPDATE orders
            SET
                delivery_person_id = ?,
                order_status_id = 4,
                delivery_person_assigned_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$delivery_person_id, $order_id]);
    }

    public static function setReadyStatus($order_id) {
        global $pdo;
        $stmt = $pdo->prepare("
            UPDATE orders
            SET order_status_id = 3
            WHERE id = ?
        ");
        $stmt->execute([$order_id]);
    }

    public static function setDeliveredStatus($order_id) {
        global $pdo;
        $stmt = $pdo->prepare("
        UPDATE orders
        SET order_status_id = 5
        WHERE id = ?
        ");
        $stmt->execute([$order_id]);
    }

    public static function cancelDelivery($order_id, $uid) {
        global $pdo;
        
        $stmt_cancel = $pdo->prepare("
            INSERT IGNORE INTO delivery_cancellation (order_id, delivery_person_id)
            VALUES (?, ?)
        ");
        $stmt_cancel->execute([$order_id, $uid]);
        
        $stmt = $pdo->prepare("
            UPDATE orders
            SET order_status_id = 3,
                delivery_person_id = NULL,
                delivery_person_assigned_at = NULL
            WHERE id = ?
        ");
        $stmt->execute([$order_id]);
    }

    public static function getLastOrder($uid) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT o.id as order_id, r.id as review_id, o.is_takeaway
            FROM orders o
            LEFT JOIN reviews r ON r.order_id = o.id
            WHERE o.customer_id = ?
            ORDER BY o.id DESC
            LIMIT 1
        ");
        $stmt->execute([$uid]);
        return $stmt->fetch();
    }


    public static function checkOrderExistsByCartId($cart_id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT id FROM orders WHERE cart_id = ?");
        $stmt->execute([$cart_id]);
        return $stmt->fetch();
    }

    public static function createOrder($cart_id, $customer_id, $order_status_id, $cook_id, $is_takeaway, $takeaway_time) {
        global $pdo;
        $cook_assigned_at = $cook_id == null ? "NULL" : "NOW()";

        $stmt = $pdo->prepare("
            INSERT INTO orders (cart_id, customer_id, cook_id, order_status_id, is_takeaway, takeaway_time, cook_assigned_at)
            VALUES (?, ?, ?, ?, ?, ?, $cook_assigned_at)
        ");
        $stmt->execute([$cart_id, $customer_id, $cook_id, $order_status_id, $is_takeaway, $takeaway_time]);
        return $pdo->lastInsertId();
    }

    public static function deliveryCanceled($order_id, $uid) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT o.* FROM orders o
            JOIN delivery_cancellation dc ON dc.order_id = o.id AND dc.delivery_person_id = ?
            WHERE o.id = ? AND o.order_status_id = 3 AND o.is_takeaway = 0
        ");
        $stmt->execute([$uid, $order_id]);
        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getNextDeliveries($uid, $limit = 3) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT o.* FROM orders o
            LEFT JOIN delivery_cancellation dc ON dc.order_id = o.id AND dc.delivery_person_id = ?
            WHERE o.order_status_id = 3 AND o.is_takeaway = 0 AND dc.order_id IS NULL
            ORDER BY o.id ASC
            LIMIT " . (int)$limit . "
        ");
        $stmt->execute([$uid]);
        return $stmt->fetchAll();
    }

    public static function getUserActiveOrders($uid, $force_ids = []) {
        global $pdo;
        $params = [$uid];
        $force_sql = "";
        
        if (!empty($force_ids)) {
            $inQuery = implode(',', array_fill(0, count($force_ids), '?'));
            $force_sql = " OR o.id IN ($inQuery)";
            $params = array_merge($params, $force_ids);
        }

        $stmt = $pdo->prepare("
            SELECT o.id, os.id as status, o.cook_id, o.delivery_person_id, o.is_takeaway
            FROM order_status os
            JOIN orders o on o.order_status_id = os.id
            WHERE o.customer_id = ? AND (os.id < 5 $force_sql)
            ORDER BY o.id DESC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getOrderStatuses($uid, $order_ids) {
        global $pdo;
        $inQuery = implode(',', array_fill(0, count($order_ids), '?'));
        $stmt = $pdo->prepare("
            SELECT o.id, os.id as status
            FROM order_status os
            JOIN orders o on o.order_status_id = os.id
            WHERE o.customer_id = ? AND o.id IN ($inQuery)
        ");
        $params = array_merge([$uid], $order_ids);
        $stmt->execute($params);
        $result = [];
        foreach($stmt->fetchAll() as $row) {
             $result[$row['id']] = $row['status'];
        }
        return $result;
    }
    
    public static function getOrderItems($order_id) {
        global $pdo;

        $stmt = $pdo->prepare("
            SELECT f.name, cf.quantity
            FROM orders o
            JOIN cart_food cf ON cf.cart_id = o.cart_id
            JOIN food f ON f.id = cf.food_id
            WHERE o.id = ?
        ");
        $stmt->execute([$order_id]);
        $foods = $stmt->fetchAll();

        $stmt = $pdo->prepare("
            SELECT m.name, cm.quantity
            FROM orders o
            JOIN cart_menu cm ON cm.cart_id = o.cart_id
            JOIN menu m ON m.id = cm.menu_id
            WHERE o.id = ?
        ");
        $stmt->execute([$order_id]);
        $menus = $stmt->fetchAll();

        return ['foods' => $foods, 'menus' => $menus];
    }

    public static function getAllRunningDeliveries() {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT o.*, os.name AS status, u.first_name, u.last_name
            FROM orders o
            JOIN order_status os ON o.order_status_id = os.id
            LEFT JOIN users u ON o.delivery_person_id = u.id
            WHERE o.is_takeaway = 0 AND o.order_status_id < 5
            ORDER BY o.id DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}