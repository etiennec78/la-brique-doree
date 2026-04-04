<?php
require_once __DIR__ . '/../db_connect.php';

class Menu {
    public static function getMenuFoods($menu_id) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT f.id as item_id, f.name, f.price, f.description, f.image_path
            FROM food f
            JOIN menu_food mf ON f.id = mf.food_id
            WHERE mf.menu_id = ?
        ");
        $stmt->execute([$menu_id]);
        return $stmt->fetchAll();
    }
}
