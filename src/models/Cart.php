<?php
require_once __DIR__ . '/../db_connect.php';

class Cart {
    public static function getUserCartId($uid) {
        global $pdo;
        $cart_id = 0;
        if (isset($_SESSION['user'])) {
            $stmt_c = $pdo->prepare("SELECT id FROM cart WHERE user_id = ? AND payment_status_id = 1 LIMIT 1");
            $stmt_c->execute([$_SESSION['user']['id']]);
            $res_c = $stmt_c->fetch();
            $cart_id = $res_c ? $res_c['id'] : 0;
        }
        return $cart_id;
    }

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

    public static function getCartMenus($uid) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT m.id, m.name, m.price, cm.quantity
            FROM cart c
            JOIN cart_menu cm ON c.id = cm.cart_id
            JOIN menu m ON cm.menu_id = m.id
            WHERE c.user_id = ? AND c.payment_status_id = 1
        ");
        $stmt->execute([$uid]);
        return $stmt->fetchAll();
    }

    public static function getCartFoods($uid) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT f.id as item_id, f.name, f.price, f.description, f.image_path, cf.quantity
            FROM cart c
            JOIN cart_food cf ON c.id = cf.cart_id
            JOIN food f ON cf.food_id = f.id
            WHERE c.user_id = ? AND c.payment_status_id = 1
        ");
        $stmt->execute([$uid]);
        return $stmt->fetchAll();
    }
}
