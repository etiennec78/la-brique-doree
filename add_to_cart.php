<?php
session_start();
include_once 'db_connect.php';

if (!isset($_SESSION['user'])) {
    // Optionally redirect to login page if they can't use cart without being logged in
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['food_id'])) {
    $food_id = (int)$_POST['food_id'];
    $user_id = $_SESSION['user']['id'];

    try {
        // Enforce user has a cart
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT id FROM cart WHERE user_id = ? AND payment_status_id = 1");
        $stmt->execute([$user_id]);
        $cart = $stmt->fetch();

        if (!$cart) {
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, payment_status_id, created_at) VALUES (?, 1, NOW())");
            $stmt->execute([$user_id]);
            $cart_id = $pdo->lastInsertId();
        } else {
            $cart_id = $cart['id'];
        }

        // Check if food already in cart
        $stmt = $pdo->prepare("SELECT quantity FROM cart_food WHERE cart_id = ? AND food_id = ?");
        $stmt->execute([$cart_id, $food_id]);
        $cart_item = $stmt->fetch();

        if ($cart_item) {
            // Update quantity
            $stmt = $pdo->prepare("UPDATE cart_food SET quantity = quantity + 1 WHERE cart_id = ? AND food_id = ?");
            $stmt->execute([$cart_id, $food_id]);
        } else {
            // Insert new item
            $stmt = $pdo->prepare("INSERT INTO cart_food (cart_id, food_id, quantity) VALUES (?, ?, 1)");
            $stmt->execute([$cart_id, $food_id]);
        }

        $pdo->commit();
    } catch (\PDOException $e) {
        $pdo->rollBack();
        // Handle error...
    }
}

$referer = $_SERVER['HTTP_REFERER'] ?? 'presentation.php';
header("Location: $referer");
exit;
