<?php
require_once __DIR__ . '/../db_connect.php';

class Food {
    public static function getTypes() {
  /*

    INPUT :

         None

    OUTPUT :

      (array) $types : variable representing an associative array of food types indexed by their ID


    SUMMARY :

    This function retrieves all food types ordered by their ID as a key-pair array.

  */
        global $pdo;
        $stmt = $pdo->prepare("SELECT id, name FROM food_type ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public static function getAllergens($foodId) {
  /*

    INPUT :

         (int) $foodId : variable representing the food item ID

    OUTPUT :

      (array) $allergens : variable representing a sequential array of allergen names


    SUMMARY :

    This function retrieves a list of allergen names associated with a specific food item ID.

  */
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


    public static function getAllAllergens() {
  /*

    INPUT :

         None

    OUTPUT :

      (array) $allergens : variable representing a sequential array of allergen names


    SUMMARY :

    This function retrieves the list of all allergens from the database.

    */
        global $pdo;
        $stmt = $pdo->prepare("
          SELECT a.name
          FROM allergen a
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }


    public static function getAll() {
  /*

    INPUT :

         None

    OUTPUT :

      (array) $foods : variable representing an array of all food items with their formatted allergen classes


    SUMMARY :

    This function fetches all food items from the database and appends their associated allergens formatted as a string of space-separated HTML-safe CSS classes.

  */
        global $pdo;
        $stmt = $pdo->prepare("SELECT id, name, price, description, image_path, food_type FROM food ORDER BY id ASC");
        $stmt->execute();
        $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($foods as &$food) {
            // Get allergens and format theme as classes
            $allergens = self::getAllergens($food['id']);
            $food['allergens_classes'] = implode(' ', array_map('strtolower', array_map('htmlspecialchars', $allergens)));
        }

        return $foods;
    }

    public static function createFood($name, $food_type, $description, $price, $image_path, $allergen_ids = []) {
        global $pdo;
        // Insert the new food
        $stmt = $pdo->prepare("INSERT INTO food (name, food_type, description, price, image_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $food_type, $description, $price, $image_path]);

        // Link allergens
        $id = $pdo->lastInsertId();
        if (!empty($allergen_ids)) {
            $stmtAllergen = $pdo->prepare("INSERT INTO food_allergen (food_id, allergen_id) VALUES (?, ?)");

            foreach ($allergen_ids as $allergen_id) {
                $stmtAllergen->execute([$food_id, $allergen_id]);
            }
        }
    }
}
