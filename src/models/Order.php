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

    public static function getAvailableDeliveryPerson() {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT u.id
            FROM users u
            JOIN role r ON u.role_id = r.id
            LEFT JOIN orders o ON o.delivery_person_id = u.id
            WHERE r.name = 'delivery_person'
            GROUP BY u.id
            ORDER BY MAX(o.delivery_person_assigned_at) ASC
        ");
        $stmt->execute();
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
}
