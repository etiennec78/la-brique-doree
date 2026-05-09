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

        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public static function setDeliveryStatus($order_id, $delivery_person_id) {
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

    public static function cancelDelivery($order_id) {
        global $pdo;
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
            SELECT o.id as order_id, r.id as review_id
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
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$cart_id, $customer_id, $cook_id, $order_status_id, $is_takeaway, $takeaway_time, $cook_assigned_at]);
        return $pdo->lastInsertId();
    }

}
