<?php
require_once __DIR__ . '/../db_connect.php';

class Order {
  public static function getUserRunningOrder($uid) {
    global $pdo;
    $stmt = $pdo->prepare("
      SELECT o.id, os.id as status, u.first_name, u.last_name
      FROM order_status os
      JOIN orders o on o.order_status_id = os.id
      LEFT JOIN users u on u.id = o.delivery_person_id
      WHERE o.customer_id = ?
    ");
    $stmt->execute([$uid]);
    return $stmt->fetch();
  }
}
