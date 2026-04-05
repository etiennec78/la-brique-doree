<?php
require_once __DIR__ . '/../db_connect.php';

class Order {
    public static function getUserRunningOrder($uid) {
        global $pdo;
        $stmt = $pdo->prepare("
        SELECT o.id, os.id as status, o.cook_id, o.delivery_person_id
        FROM order_status os
        JOIN orders o on o.order_status_id = os.id
        WHERE o.customer_id = ?
        ");
        $stmt->execute([$uid]);
        return $stmt->fetch();
    }

    public static function getOrdersFromState($order_names) {
        global $pdo;
        $placeholders = implode(',', array_fill(0, count($order_names), '?'));
        $stmt = $pdo->prepare("
            SELECT o.id, u.first_name, u.last_name
            FROM orders o
            JOIN users u ON o.customer_id = u.id
            JOIN order_status os ON o.order_status_id = os.id
            WHERE os.name IN ($placeholders)
        ");
        $stmt->execute($order_names);
        return $stmt->fetchAll();
    }
}
