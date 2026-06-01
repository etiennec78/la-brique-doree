<?php
require_once __DIR__ . '/../db_connect.php';

class Coupon {
    public static function addCouponToCart($coupon_id, $cart_id) {
  /*

    INPUT :

         (int) $coupon_id : variable representing the coupon ID
     (int) $cart_id : variable representing the cart ID

    OUTPUT :

      None


    SUMMARY :

    This function associates a specific coupon with a given cart by updating the cart record.

  */
        global $pdo;
        $stmt = $pdo->prepare("UPDATE cart SET coupon_id = ? WHERE id = ?");
        $stmt->execute([$coupon_id, $cart_id]);
    }

    public static function addGlobalReductionToCart($user_id, $cart_id) {
  /*

    INPUT :

         (int) $user_id : variable representing the user ID
     (int) $cart_id : variable representing the cart ID

    OUTPUT :

      None


    SUMMARY :

    This function associates the user's global reduction with a given cart by updating the cart record.

  */
        global $pdo;
        $stmt = $pdo->prepare("
          UPDATE cart 
          SET global_reduction = (SELECT global_reduction FROM users WHERE id = ?) 
          WHERE id = ?
        ");
        $stmt->execute([$user_id, $cart_id]);
    }

    public static function getCoupon($coupon) {
  /*

    INPUT :

         (str) $coupon : variable representing the coupon code string

    OUTPUT :

      (array|bool) $result : variable representing the coupon details array, or false if not found


    SUMMARY :

    This function fetches the details of a coupon from the database matching the provided coupon code.

  */
        global $pdo;
        $stmt_c = $pdo->prepare("SELECT * FROM coupon WHERE code = ?");
        $stmt_c->execute([$coupon]);
        return $stmt_c->fetch();
    }

    public static function getCouponFromCart($cart_id) {
  /*

    INPUT :

         (int) $cart_id : variable representing the cart ID

    OUTPUT :

      (array|bool) $result : variable representing the coupon details array attached to the cart, or false if not found


    SUMMARY :

    This function retrieves all information about a coupon applied to a specific cart ID.

  */
      global $pdo;
      $stmt = $pdo->prepare("
        SELECT co.*
        FROM coupon co
        JOIN cart ca ON ca.coupon_id = co.id
        WHERE ca.id = ?
      ");
      $stmt->execute([$cart_id]);
      return $stmt->fetch();
    }

    public static function getGlobalReductionFromCart($cart_id) {
  /*

    INPUT :

         (int) $cart_id : variable representing the cart ID

    OUTPUT :

      (float) $reduction : variable representing the global reduction attached to the cart


    SUMMARY :

    This function retrieves the global reduction applied to a specific cart ID.

  */
      global $pdo;
      $stmt = $pdo->prepare("
        SELECT global_reduction
        FROM cart
        WHERE id = ?
      ");
      $stmt->execute([$cart_id]);
      $reduction = $stmt->fetchColumn();
      return $reduction !== false ? (float)$reduction : 0.0;
    }
}
