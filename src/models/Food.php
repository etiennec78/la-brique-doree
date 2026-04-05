<?php
require_once __DIR__ . '/../db_connect.php';

class Food {
    public static function getTypes() {
        global $pdo;
        $stmt = $pdo->prepare("SELECT id, name FROM food_type ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getByType($typeId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT id, name, price, description, image_path FROM food f WHERE f.food_type = ?");
        $stmt->execute([$typeId]);
        return $stmt->fetchAll();
    }

    public static function getAllergens($foodId) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT a.name
            FROM allergen a
            JOIN food_allergen fa ON fa.allergen_id = a.id
            JOIN food f ON fa.food_id = f.id WHERE f.id = ?
        ");
        $stmt->execute([$foodId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
