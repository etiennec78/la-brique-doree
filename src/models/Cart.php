<?php
require_once __DIR__ . '/../db_connect.php';

class Cart {
    public static function getUserCartId($uid) {
        /*
         
          INPUT :
                 
                    (int) $uid : variable representing the user ID
          
          OUTPUT :

          (int) $id : variable representing the active cart ID, or 0 if not found

          
          SUMMARY :
         
        This function fetches the active cart ID for a specific user from the database.

        */
        global $pdo;
        $stmt_c = $pdo->prepare("SELECT id FROM cart WHERE user_id = ? AND payment_status_id = 1 LIMIT 1");
        $stmt_c->execute([$uid]);
        $res_c = $stmt_c->fetch();
        return $res_c ? $res_c['id'] : 0;
    }

    public static function createCart($uid) {
        /*
         
          INPUT :
                 
                    (int) $uid : variable representing the user ID
          
          OUTPUT :

          (int) $id : variable representing the newly created cart ID

          
          SUMMARY :
         
        This function creates a new cart record for the user and returns its generated ID.

        */
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, payment_status_id, created_at) VALUES (?, 1, NOW())");
        $stmt->execute([$uid]);
        return $pdo->lastInsertId();
    }

    private static function getItemCount($cart_id, $table) {
        /*
         
          INPUT :
                 
                    (int) $cart_id : variable representing the cart ID
        (str) $table : variable representing the target table name
          
          OUTPUT :

          (int) $count : variable representing the sum of item quantities

          
          SUMMARY :
         
        This function counts the total items in a specified cart table by summing their quantities.

        */
        global $pdo;
        $stmt = $pdo->prepare("SELECT SUM(quantity) FROM $table WHERE cart_id = ?");
        $stmt->execute([$cart_id]);
        return $stmt->fetchColumn() ?: 0;
    }

    public static function getCartCount() {
        /*
         
          INPUT :
                 
                    None
          
          OUTPUT :

          (int) $total : variable representing the combined total items count

          
          SUMMARY :
         
        This function computes the overall total count of items across all types in the active user session's cart.

        */
        if (isset($_SESSION['user'])) {
            try {
                $uid = $_SESSION['user']['id'];
                $cart_id = self::getUserCartId($uid);

                if ($cart_id) {
                    $count_food = self::getItemCount($cart_id, 'cart_food');
                    $count_menu = self::getItemCount($cart_id, 'cart_menu');
                    return (int)$count_food + (int)$count_menu;
                }
            } catch (\PDOException $error) {
                $_SESSION['error'] = "Erreur de panier : " . $error->getMessage();
                error_log("Cart error : " . $error->getMessage());
            }
        }
        return 0;
    }

    public static function getCartItems($uid, $item_type, $cart_id=-1) {
        /*
         
          INPUT :
                 
                    (int) $uid : variable representing the user ID
        (str) $item_type : variable representing the item type ('food' or 'menu')
        (int) $cart_id : variable representing the cart ID, defaults to -1 for active cart
          
          OUTPUT :

          (array) $items : variable representing the retrieved list of cart items

          
          SUMMARY :
         
        This function retrieves all items matching a specific type for a user's cart from the database.

        */
        global $pdo;

        // Check arguments
        if (
            !in_array($item_type, ["menu", "food"])
            || !is_numeric($cart_id)
        ) return [];

        // Get the current cart, or the one specified by $cart_id
        $condition = ($cart_id == -1) ? "c.payment_status_id = 1" : "c.id = $cart_id";

        // Get the image and type only for food items
        $select = ($item_type == "food") ? ", mf.image_path, mf.food_type" : "";
        $order = ($item_type == "food") ? "ORDER BY mf.food_type ASC" : "";

        $stmt = $pdo->prepare("
            SELECT mf.id, mf.name, mf.price, mf.description, cmf.quantity $select
            FROM cart c
            JOIN cart_$item_type cmf ON c.id = cmf.cart_id
            JOIN $item_type mf ON cmf.$item_type"."_id = mf.id
            WHERE c.user_id = ? AND $condition
            $order
        ");
        $stmt->execute([$uid]);
        return $stmt->fetchAll();
    }

    public static function getItemQuantity($table_name, $foreign_key, $cart_id, $item_id) {
        /*
         
          INPUT :
                 
                    (str) $table_name : variable representing the table name
        (str) $foreign_key : variable representing the item foreign key name
        (int) $cart_id : variable representing the cart ID
        (int) $item_id : variable representing the item ID
          
          OUTPUT :

          (int) $quantity : variable representing the quantity of the item in the cart

          
          SUMMARY :
         
        This function returns the existing quantity of a specific item in a cart table.

        */
        global $pdo;
        $stmt = $pdo->prepare("SELECT quantity FROM $table_name WHERE cart_id = ? AND $foreign_key = ?");
        $stmt->execute([$cart_id, $item_id]);
        $res = $stmt->fetch();
        return $res ? (int)$res['quantity'] : 0;
    }

    public static function incrementItemQuantity($table_name, $foreign_key, $cart_id, $item_id) {
        /*
         
          INPUT :
                 
                    (str) $table_name : variable representing the table name
        (str) $foreign_key : variable representing the item foreign key name
        (int) $cart_id : variable representing the cart ID
        (int) $item_id : variable representing the item ID
          
          OUTPUT :

          (bool) $result : variable representing the execution success status

          
          SUMMARY :
         
        This function increments the quantity of a given item within a cart by one.

        */
        global $pdo;
        $stmt = $pdo->prepare("UPDATE $table_name SET quantity = quantity + 1 WHERE cart_id = ? AND $foreign_key = ?");
        return $stmt->execute([$cart_id, $item_id]);
    }

    public static function decrementItemQuantity($table_name, $foreign_key, $cart_id, $item_id) {
        /*
         
          INPUT :
                 
                    (str) $table_name : variable representing the table name
        (str) $foreign_key : variable representing the item foreign key name
        (int) $cart_id : variable representing the cart ID
        (int) $item_id : variable representing the item ID
          
          OUTPUT :

          (bool) $result : variable representing the execution success status

          
          SUMMARY :
         
        This function decrements the quantity of a given item within a cart by one.

        */
        global $pdo;
        $stmt = $pdo->prepare("UPDATE $table_name SET quantity = quantity - 1 WHERE cart_id = ? AND $foreign_key = ?");
        return $stmt->execute([$cart_id, $item_id]);
    }

    public static function removeItem($table_name, $foreign_key, $cart_id, $item_id) {
        /*
         
          INPUT :
                 
                    (str) $table_name : variable representing the table name
        (str) $foreign_key : variable representing the item foreign key name
        (int) $cart_id : variable representing the cart ID
        (int) $item_id : variable representing the item ID
          
          OUTPUT :

          (bool) $result : variable representing the execution success status

          
          SUMMARY :
         
        This function deletes a specific item entirely from the targeted cart table.

        */
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM $table_name WHERE cart_id = ? AND $foreign_key = ?");
        return $stmt->execute([$cart_id, $item_id]);
    }

    public static function addItem($table_name, $foreign_key, $cart_id, $item_id) {
        /*
         
          INPUT :
                 
                    (str) $table_name : variable representing the table name
        (str) $foreign_key : variable representing the item foreign key name
        (int) $cart_id : variable representing the cart ID
        (int) $item_id : variable representing the item ID
          
          OUTPUT :

          (bool) $result : variable representing the execution success status

          
          SUMMARY :
         
        This function inserts a new item entry with a quantity of one into the cart table.

        */
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO $table_name (cart_id, $foreign_key, quantity) VALUES (?, ?, 1)");
        return $stmt->execute([$cart_id, $item_id]);
    }

    public static function updateItem($user_id, $item_id, $item_type, $action, $amount = null) {
        /*
         
          INPUT :
                 
                    (int) $user_id : variable representing the user ID
        (int) $item_id : variable representing the item ID
        (str) $item_type : variable representing the item type ('food' or 'menu')
        (str) $action : variable representing the operation to run ('add', 'remove', or 'set')
        (int|null) $amount : variable representing the exact quantity to set
          
          OUTPUT :

          None : This function does not return a value

          
          SUMMARY :
         
        This function handles adding, removing, or setting explicit quantities for items in the user's cart, enforcing limit validation and database transactions.

        */
        global $pdo;

        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }
        
        $table_name = $item_type === 'food' ? 'cart_food' : 'cart_menu';
        $foreign_key = $item_type === 'food' ? 'food_id' : 'menu_id';

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
                elseif ($action === 'set' && $amount !== null) {
                    if ($amount <= 0) {
                        self::removeItem($table_name, $foreign_key, $cart_id, $item_id);
                    } elseif ($amount <= 9) {
                        $stmt = $pdo->prepare("UPDATE $table_name SET quantity = ? WHERE cart_id = ? AND $foreign_key = ?");
                        $stmt->execute([$amount, $cart_id, $item_id]);
                    } else {
                        $_SESSION['error'] = 'Vous ne pouvez pas ajouter plus de 9 fois le même article dans votre panier.';
                    }
                }
            } 
            
            elseif ($action === 'add') {
                self::addItem($table_name, $foreign_key, $cart_id, $item_id);
            }
            elseif ($action === 'set' && $amount !== null && $amount > 0) {
                if ($amount <= 9) {
                    $stmt = $pdo->prepare("INSERT INTO $table_name (cart_id, $foreign_key, quantity) VALUES (?, ?, ?)");
                    $stmt->execute([$cart_id, $item_id, $amount]);
                } else {
                    $_SESSION['error'] = 'Vous ne pouvez pas ajouter plus de 9 fois le même article dans votre panier.';
                }
            }

            $pdo->commit();
        } catch (\PDOException $error) {
            $pdo->rollBack();
            $_SESSION['error'] = "Erreur de mise à jour du panier : " . $error->getMessage();
            error_log("Cart update error: " . $error->getMessage());
        }
    }


    public static function markCartAsPaid($cart_id, $user_id) {
        /*
         
          INPUT :
                 
                    (int) $cart_id : variable representing the cart ID
        (int) $user_id : variable representing the user ID
          
          OUTPUT :

          (bool) $result : variable representing the execution success status

          
          SUMMARY :
         
        This function updates the payment status of a specific cart to mark it as paid.

        */
        global $pdo;
        $stmt = $pdo->prepare("UPDATE cart SET payment_status_id = 2 WHERE id = ? AND user_id = ?");
        return $stmt->execute([$cart_id, $user_id]);
    }

}
