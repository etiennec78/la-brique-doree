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

    public static function getReviews() {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT u.first_name, u.last_name, r.product_stars, r.delivery_stars, r.comment
            FROM reviews r
            JOIN users u ON r.user_id = u.id
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

