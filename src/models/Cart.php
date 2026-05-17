<?php
require_once __DIR__ . '/../db_connect.php';

class Cart {
    public static function getUserCartId($uid) {
        global $pdo;
        $stmt_c = $pdo->prepare("SELECT id FROM cart WHERE user_id = ? AND payment_status_id = 1 LIMIT 1");
        $stmt_c->execute([$uid]);
        $res_c = $stmt_c->fetch();
        return $res_c ? $res_c['id'] : 0;
    }

    public static function createCart($uid) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, payment_status_id, created_at) VALUES (?, 1, NOW())");
        $stmt->execute([$uid]);
        return $pdo->lastInsertId();
    }

    public static function getFoodCount($cart_id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart_food WHERE cart_id = ?");
        $stmt->execute([$cart_id]);
        return $stmt->fetchColumn() ?: 0;
    }

    public static function getMenuCount($cart_id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart_menu WHERE cart_id = ?");
        $stmt->execute([$cart_id]);
        return $stmt->fetchColumn() ?: 0;
    }

    public static function getCartCount() {
        if (isset($_SESSION['user'])) {
            try {
                $uid = $_SESSION['user']['id'];
                $cart_id = self::getUserCartId($uid);

                if ($cart_id) {
                    $count_food = self::getFoodCount($cart_id);
                    $count_menu = self::getMenuCount($cart_id);
                    return (int)$count_food + (int)$count_menu;
                }
            } catch (\PDOException $e) {
                error_log("Cart error: " . $e->getMessage());
            }
        }
        return 0;
    }

    public static function getCartItems($uid, $item_type, $cart_id=-1) {
        global $pdo;

        // Check arguments
        if (
            !in_array($item_type, ["menu", "food"])
            || !is_numeric($cart_id)
        ) return [];

        // Get the current cart, or the one specified by $cart_id
        $condition = ($cart_id == -1) ? "c.payment_status_id = 1" : "c.id = $cart_id";

        // Get the image only for food type
        $select = ($item_type == "food") ? ", mf.image_path" : "";

        $stmt = $pdo->prepare("
            SELECT mf.id, mf.name, mf.price, mf.description, cmf.quantity $select
            FROM cart c
            JOIN cart_$item_type cmf ON c.id = cmf.cart_id
            JOIN $item_type mf ON cmf.$item_type"."_id = mf.id
            WHERE c.user_id = ? AND $condition
        ");
        $stmt->execute([$uid]);
        return $stmt->fetchAll();
    }

    public static function getItemQuantity($table_name, $foreign_key, $cart_id, $item_id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT quantity FROM $table_name WHERE cart_id = ? AND $foreign_key = ?");
        $stmt->execute([$cart_id, $item_id]);
        $res = $stmt->fetch();
        return $res ? (int)$res['quantity'] : 0;
    }

    public static function incrementItemQuantity($table_name, $foreign_key, $cart_id, $item_id) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE $table_name SET quantity = quantity + 1 WHERE cart_id = ? AND $foreign_key = ?");
        return $stmt->execute([$cart_id, $item_id]);
    }

    public static function decrementItemQuantity($table_name, $foreign_key, $cart_id, $item_id) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE $table_name SET quantity = quantity - 1 WHERE cart_id = ? AND $foreign_key = ?");
        return $stmt->execute([$cart_id, $item_id]);
    }

    public static function removeItem($table_name, $foreign_key, $cart_id, $item_id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM $table_name WHERE cart_id = ? AND $foreign_key = ?");
        return $stmt->execute([$cart_id, $item_id]);
    }

    public static function addItem($table_name, $foreign_key, $cart_id, $item_id) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO $table_name (cart_id, $foreign_key, quantity) VALUES (?, ?, 1)");
        return $stmt->execute([$cart_id, $item_id]);
    }

    public static function updateItem($user_id, $item_id, $item_type, $action) {
        global $pdo;
        
        $table_name = $item_type === 'food' ? 'cart_food' : 'cart_menu';
        $foreign_key = $item_type === 'food' ? 'food_id' : 'menu_id';
        $_SESSION['error'] = NULL;

        try {
            $pdo->beginTransaction();

            $cart_id = self::getUserCartId($user_id);

            if (!$cart_id) {
                $cart_id = self::createCart($user_id);
            }

            $current_quantity = self::getItemQuantity($table_name, $foreign_key, $cart_id, $item_id);

            if ($current_quantity > 0) {
                if ($action === 'remove') {
                    if ($current_quantity > 1) {
                        self::decrementItemQuantity($table_name, $foreign_key, $cart_id, $item_id);
                    } 
                    
                    else {
                        self::removeItem($table_name, $foreign_key, $cart_id, $item_id);
                    }
                }

                else if ($action === 'add' && $current_quantity < 9) {
                    self::incrementItemQuantity($table_name, $foreign_key, $cart_id, $item_id);
                } 

                elseif ($action === 'add' && $current_quantity >= 9 ) {
                    $_SESSION['error'] = 'Vous ne pouvez pas ajouter plus de 9 fois le même article dans votre panier.';
                }
            } 
            
            elseif ($action === 'add') {
                self::addItem($table_name, $foreign_key, $cart_id, $item_id);
            }

            $pdo->commit();
        } catch (\PDOException $e) {
            $pdo->rollBack();
            error_log("Cart update error: " . $e->getMessage());
        }
    }


    public static function markCartAsPaid($cart_id, $user_id) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE cart SET payment_status_id = 2 WHERE id = ? AND user_id = ?");
        return $stmt->execute([$cart_id, $user_id]);
    }

}
