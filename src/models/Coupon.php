<?php
require_once __DIR__ . '/../db_connect.php';

class Coupon {
    public static function addCouponToCart($coupon_id, $cart_id) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE cart SET coupon_id = ? WHERE id = ?");
        $stmt->execute([$coupon_id, $cart_id]);
    }

    public static function getCoupon($coupon) {
        global $pdo;
        $stmt_c = $pdo->prepare("SELECT * FROM coupon WHERE code = ?");
        $stmt_c->execute([$coupon]);
        return $stmt_c->fetch();
    }

    public static function getCouponFromUser($uid) {
      global $pdo;
      $stmt_c = $pdo->prepare("
        SELECT co.id, co.code, co.discount_percent, co.expiration_date
        FROM coupon co
        JOIN cart ca ON ca.coupon_id = co.id
        WHERE ca.user_id = ?
      ");
      $stmt_c->execute([$uid]);
      return $stmt_c->fetch();
    }
}
