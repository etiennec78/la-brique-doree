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
            SELECT f.id as item_id, f.name, f.price, f.description, f.image_path, mf.quantity
            FROM food f
            JOIN menu_food mf ON f.id = mf.food_id
            WHERE mf.menu_id = ?
        ");
        $stmt->execute([$menu_id]);
        return $stmt->fetchAll();
    }
}
