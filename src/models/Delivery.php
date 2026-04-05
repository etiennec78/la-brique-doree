<?php
require_once __DIR__ . '/../db_connect.php';

class Delivery {
    public static function getDeliveries($uid) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT o.id, u.first_name, u.last_name, u.street_nb, u.street_nb_suf, u.street, u.town, u.zip_code, u.intercom_code
            FROM orders o
            LEFT JOIN users u ON o.customer_id = u.id
            JOIN order_status os ON os.id = o.order_status_id
            WHERE o.delivery_person_id = ? AND os.name = 'shipping'
        ");
        $stmt->execute([$uid]);
        return $stmt->fetchAll();
    }
}
