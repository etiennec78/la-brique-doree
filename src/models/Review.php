<?php
require_once __DIR__ . '/../db_connect.php';

class Review {
    public static function addReview($order_id, $product, $delivery, $comment) {
        global $pdo;
        $stmt = $pdo->prepare("
          INSERT INTO reviews (order_id, product_stars, delivery_stars, comment)
          VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$order_id, $product, $delivery, $comment]);
    }

    public static function updateReview($review_id, $product, $delivery, $comment) {
        global $pdo;
        
        $stmt = $pdo->prepare("
            UPDATE reviews
            SET product_stars = ?, delivery_stars = ?, comment = ?
            WHERE id = ?
        ");
        $stmt->execute([$product, $delivery, $comment, $review_id]);
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

    public static function getReviewer($review_id) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT u.id
            FROM reviews r
            JOIN orders o ON o.id = r.order_id
            JOIN users u ON o.customer_id = u.id
            WHERE r.id = ?
        ");
        $stmt->execute([$review_id]);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }
}

