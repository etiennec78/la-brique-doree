<?php
require_once __DIR__ . '/../db_connect.php';

class Menu {
    public static function getMenus() {
  /*

    INPUT :

         None

    OUTPUT :

      (array) $menus : variable representing an array of all menus ordered by ID


    SUMMARY :

    This function retrieves all menus from the database ordered by their ID.

  */
        global $pdo;
        $stmtMenu = $pdo->prepare("SELECT id, name, price, description FROM menu ORDER BY id ASC");
        $stmtMenu->execute();
        return $stmtMenu->fetchAll();
    }

    public static function getMenuFoods($menu_id) {
  /*

    INPUT :

         (int) $menu_id : variable representing the menu ID

    OUTPUT :

      (array) $foods : variable representing an array of food items belonging to the menu along with their quantities


    SUMMARY :

    This function fetches all food items associated with a specific menu ID, including their individual details and quantities.

  */
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT f.id, f.name, f.price, f.description, f.image_path, mf.quantity
            FROM food f
            JOIN menu_food mf ON f.id = mf.food_id
            WHERE mf.menu_id = ?
        ");
        $stmt->execute([$menu_id]);
        return $stmt->fetchAll();
    }

    private static function getFoodQuantity($menu_id, $food_id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT quantity FROM menu_food WHERE menu_id = ? AND food_id = ?");
        $stmt->execute([$menu_id, $food_id]);
        $result = $stmt->fetchColumn();
        return $result !== false ? (int)$result : 0;
    }

    private static function insertFood($menu_id, $food_id, $quantity) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO menu_food (menu_id, food_id, quantity) VALUES (?, ?, ?)");
        return $stmt->execute([$menu_id, $food_id, $quantity]);
    }

    private static function removeFood($menu_id, $food_id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM menu_food WHERE menu_id = ? AND food_id = ?");
        return $stmt->execute([$menu_id, $food_id]);
    }

    private static function incrementFoodQuantity($menu_id, $food_id) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE menu_food SET quantity = quantity + 1 WHERE menu_id = ? AND food_id = ?");
        return $stmt->execute([$menu_id, $food_id]);
    }

    private static function decrementFoodQuantity($menu_id, $food_id) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE menu_food SET quantity = quantity - 1 WHERE menu_id = ? AND food_id = ?");
        return $stmt->execute([$menu_id, $food_id]);
    }

    private static function setFoodQuantity($menu_id, $food_id, $quantity) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE menu_food SET quantity = ? WHERE menu_id = ? AND food_id = ?");
        return $stmt->execute([$quantity, $menu_id, $food_id]);
    }

    public static function updateName($menu_id, $name) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE menu SET name = ? WHERE id = ?");
        return $stmt->execute([$name, $menu_id]);
    }

    public static function updateDescription($menu_id, $description) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE menu SET description = ? WHERE id = ?");
        return $stmt->execute([$description, $menu_id]);
    }

    public static function updatePrice($menu_id, $price) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE menu SET price = ? WHERE id = ?");
        return $stmt->execute([$price, $menu_id]);
    }

    public static function updateItem($menu_id, $food_id, $action, $amount = null) {
        $current_quantity = self::getFoodQuantity($menu_id, $food_id);

        if ($current_quantity > 0) {
            if ($action === 'remove') {
                if ($current_quantity > 1) {
                    self::decrementFoodQuantity($menu_id, $food_id);
                } else {
                    self::removeFood($menu_id, $food_id);
                }
            } else if ($action === 'add') {
                  self::incrementFoodQuantity($menu_id, $food_id);
            } else if ($action === 'set' && $amount !== null) {
                if ($amount <= 0) {
                    self::removeFood($menu_id, $food_id);
                } else {
                    self::setFoodQuantity($menu_id, $food_id, $amount);
                }
            }
        } else {
          if ($action === 'add') {
              self::insertFood($menu_id, $food_id, 1);
          } elseif ($action === 'set' && $amount !== null && $amount > 0) {
              self::insertFood($menu_id, $food_id, $amount);
          }
        }
        return true;
    }
}
