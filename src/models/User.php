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

    public static function getAllUsersInfo() {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT u.id, u.email, u.first_name, u.last_name, u.global_reduction, u.banned, r.name AS role,
            (
                COALESCE(SUM(CASE WHEN ps.name = 'pending' THEN m.total_menu_quantity ELSE 0 END), 0)
                + COALESCE(SUM(CASE WHEN ps.name = 'pending' THEN cf.total_food_quantity ELSE 0 END), 0)
            ) AS total_quantity
            FROM users u
            LEFT JOIN role r ON u.role_id = r.id
            LEFT JOIN cart c ON u.id = c.user_id
            LEFT JOIN payment_status ps ON c.payment_status_id = ps.id
            LEFT JOIN (
                SELECT cart_id, SUM(quantity) AS total_menu_quantity
                FROM cart_menu
                GROUP BY cart_id
            ) m ON c.id = m.cart_id
            LEFT JOIN (
                SELECT cart_id, SUM(quantity) AS total_food_quantity
                FROM cart_food
                GROUP BY cart_id
            ) cf ON c.id = cf.cart_id
            GROUP BY u.id, u.email, u.first_name, u.last_name, r.name;
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getUserData($uid) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT email, first_name, last_name, phone, birth_date, street_nb, street_nb_suf, street, town, zip_code, intercom_code, banned, id
            FROM users u
            WHERE u.id = ?
        ");
        $stmt->execute([$uid]);
        return $stmt->fetch();
    }

    public static function getUsersFromRole($role) {
        global $pdo;
        $stmt_users = $pdo->prepare("
            SELECT u.id, u.first_name, u.last_name
            FROM users u
            JOIN role r on u.role_id = r.id
            WHERE r.name = ?
        ");
        $stmt_users->execute([$role]);
        return $stmt_users->fetchAll();
    }

    public static function userHasName($uid) {
        $user_data = self::getUserData($uid);
        return (
            $user_data
            and !empty($user_data['first_name'])
            and !empty($user_data['last_name'])
        );
    }

    public static function getGlobalReduction($uid) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT global_reduction FROM users u WHERE u.id = ?");
        $stmt->execute([$uid]);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public static function applyGlobalReduction($uid, $reduction) {
        global $pdo;
        $stmt = $pdo->prepare("
            UPDATE users
            SET global_reduction = $reduction
            WHERE id = ?
        ");
        $stmt->execute([$uid]);
    }

    public static function getUserRole($uid) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT r.name
            FROM users u
            JOIN role r ON r.id = u.role_id
            WHERE u.id = ?
        ");
        $stmt->execute([$uid]);
        $result = $stmt->fetch();
        return $result ? $result['name'] : null;
    }
}
