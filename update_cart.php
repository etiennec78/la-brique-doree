<?php
session_start();
include_once 'db_connect.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'], $_POST['item_type'], $_POST['action'])) {
    $item_id = (int)$_POST['item_id'];
    $item_type = $_POST['item_type'];
    $action = $_POST['action'];
    $user_id = $_SESSION['user']['id'];

    if (!in_array($item_type, ['food', 'menu'])) {
        header('Location: commands.php');
        exit;
    }

    $table_name = $item_type === 'food' ? 'cart_food' : 'cart_menu';
    $foreign_key = $item_type === 'food' ? 'food_id' : 'menu_id';

    try {
        $pdo->beginTransaction();

        // Get active cart
        $stmt = $pdo->prepare("SELECT id FROM cart WHERE user_id = ? AND payment_status_id = 1");
        $stmt->execute([$user_id]);
        $cart = $stmt->fetch();

        if ($cart) {
            $cart_id = $cart['id'];
        } else {
        
        $creerPanier = $pdo->prepare("INSERT INTO cart (user_id, payment_status_id, created_at) VALUES (?, 1, NOW())");
    
        $creerPanier->execute([$user_id]);
    
        $cart_id = $pdo->lastInsertId();
}

        // Get current quantity
        $stmt = $pdo->prepare("SELECT quantity FROM $table_name WHERE cart_id = ? AND $foreign_key = ?");
        $stmt->execute([$cart_id, $item_id]);
        $cart_item = $stmt->fetch();

        if ($action === 'add') {
            if ($cart_item) {
                if ((int)$cart_item['quantity'] < 9) { 
                    $stmt = $pdo->prepare("UPDATE $table_name SET quantity = quantity + 1 WHERE cart_id = ? AND $foreign_key = ?");
                    $stmt->execute([$cart_id, $item_id]);
                }
            } else {
                $stmt = $pdo->prepare("INSERT INTO $table_name (cart_id, $foreign_key, quantity) VALUES (?, ?, 1)");
                $stmt->execute([$cart_id, $item_id]);
            }
        } elseif ($action === 'remove' && $cart_item) {
            $current_quantity = (int)$cart_item['quantity'];
            if ($current_quantity > 1) {
                $stmt = $pdo->prepare("UPDATE $table_name SET quantity = quantity - 1 WHERE cart_id = ? AND $foreign_key = ?");
                $stmt->execute([$cart_id, $item_id]);
            } else {
                // Remove item completely
                $stmt = $pdo->prepare("DELETE FROM $table_name WHERE cart_id = ? AND $foreign_key = ?");
                $stmt->execute([$cart_id, $item_id]);
            }
        }

        $pdo->commit();
    } catch (\PDOException $e) {
        $pdo->rollBack();
        error_log("Cart update error: " . $e->getMessage());
    }
}

$referer = $_SERVER['HTTP_REFERER'] ?? 'commands.php';
header("Location: $referer");
exit;
