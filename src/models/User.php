<?php
require_once __DIR__ . '/../db_connect.php';

class User {
    public static function findByEmail($email) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT u.*, r.name as role_name FROM users u JOIN role r ON u.role_id = r.id WHERE u.email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($email, $password) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, role_id, inscription_date) VALUES (?, ?, 1, NOW())");
        $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT)]);
        return $pdo->lastInsertId();
    }
}
