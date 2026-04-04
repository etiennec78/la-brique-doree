<?php
require_once __DIR__ . '/../db_connect.php';

class Cart {
    public static function getCartCount() {
        global $pdo;
        $cart_count = 0;
        if (isset($_SESSION['user']) && isset($pdo)) {
            try {
                $stmt = $pdo->prepare("SELECT id FROM cart WHERE user_id = ? AND payment_status_id = 1");
                $stmt->execute([$_SESSION['user']['id']]);
                $cart = $stmt->fetch();

                if ($cart) {
                      $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart_food WHERE cart_id = ?");
                      $stmt->execute([$cart['id']]);
                      $count_food = $stmt->fetchColumn() ?: 0;

                      $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart_menu WHERE cart_id = ?");
                      $stmt->execute([$cart['id']]);
                      $count_menu = $stmt->fetchColumn() ?: 0;

                      $cart_count = (int)$count_food + (int)$count_menu;
                  }
            } catch (\PDOException $e) {
                error_log("Cart error: " . $e->getMessage());
            }
        }
        return $cart_count;
    }
}
