<?php
require_once __DIR__ . '/../db_connect.php';

class Review {
    public static function addReview($user_id, $product, $delivery, $comment) {
        global $pdo;
        $stmt = $pdo->prepare("
          INSERT INTO reviews (user_id, product_stars, delivery_stars, comment)
          VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$user_id, $product, $delivery, $comment]);
    }

    public static function updateReview($id, $user_id, $is_admin, $product, $delivery, $comment) {
        global $pdo;
        
        if ($is_admin) {
            $stmt = $pdo->prepare("
              UPDATE reviews 
              SET product_stars = ?, delivery_stars = ?, comment = ? 
              WHERE id = ?
            ");
            $stmt->execute([$product, $delivery, $comment, $id]);
        } else {
            $stmt = $pdo->prepare("
              UPDATE reviews 
              SET product_stars = ?, delivery_stars = ?, comment = ? 
              WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$product, $delivery, $comment, $id, $user_id]);
        }
    }

    public static function getReviews() {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT r.id, u.id as user_id, u.first_name, u.last_name, r.product_stars, r.delivery_stars, r.comment
            FROM reviews r
            JOIN orders o ON o.id = r.order_id
            JOIN users u ON o.customer_id = u.id
            ORDER BY r.id DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

