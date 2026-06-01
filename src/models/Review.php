<?php
require_once __DIR__ . '/../db_connect.php';

class Review {
    public static function addReview($order_id, $product, $delivery, $comment) {
  /*

    INPUT :

         (int) $order_id : variable representing the order ID
     (int) $product : variable representing the product rating stars
     (int) $delivery : variable representing the delivery rating stars
     (str) $comment : variable representing the review comment text

    OUTPUT :

      None


    SUMMARY :

    This function inserts a new customer review record into the database for a completed order.

  */
        global $pdo;
        $stmt = $pdo->prepare("
          INSERT INTO reviews (order_id, product_stars, delivery_stars, comment)
          VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$order_id, $product, $delivery, $comment]);
    }

    public static function updateReview($review_id, $product, $delivery, $comment) {
  /*

    INPUT :

         (int) $review_id : variable representing the review ID
     (int) $product : variable representing the product rating stars
     (int) $delivery : variable representing the delivery rating stars
     (str) $comment : variable representing the review comment text

    OUTPUT :

      None


    SUMMARY :

    This function updates an existing review record with new ratings and comment text.

  */
        global $pdo;
        
        $stmt = $pdo->prepare("
            UPDATE reviews
            SET product_stars = ?, delivery_stars = ?, comment = ?
            WHERE id = ?
        ");
        $stmt->execute([$product, $delivery, $comment, $review_id]);
    }

    public static function getReviews() {
  /*

    INPUT :

         None

    OUTPUT :

      (array) $reviews : variable representing an array of all reviews with customer details, ordered by newest first


    SUMMARY :

    This function fetches all customer reviews along with the respective reviewer's name and ID.

  */
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT r.id, u.id as user_id, u.first_name, u.last_name, r.product_stars, r.delivery_stars, r.comment
            FROM reviews r
            JOIN orders o ON o.id = r.order_id
            JOIN users u ON o.user_id = u.id
            ORDER BY r.id DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getReviewer($review_id) {
  /*

    INPUT :

         (int) $review_id : variable representing the review ID

    OUTPUT :

      (int|bool) $user_id : variable representing the user ID of the reviewer, or false if not found


    SUMMARY :

    This function retrieves the customer user ID associated with a specific review ID.

  */
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT u.id
            FROM reviews r
            JOIN orders o ON o.id = r.order_id
            JOIN users u ON o.user_id = u.id
            WHERE r.id = ?
        ");
        $stmt->execute([$review_id]);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }
}

