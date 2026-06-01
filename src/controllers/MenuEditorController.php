<?php

class MenuEditorController extends Controller {
    public function index() {
        /*
            INPUT:
                None

            OUTPUT:
                None


            SUMMARY:
                Requires role level 2 or 3, aggregates existing menus while resolving internal food allergen classifications, collects sorted food variants, and presents the menu configuration dashboard.
        */
        $this->requireRole([2, 3]);
        require_once __DIR__ . '/../models/Menu.php';
        require_once __DIR__ . '/../models/Order.php';
        require_once __DIR__ . '/../models/Food.php';

        $menus = Menu::getMenus();
        foreach($menus as &$menu) {
            $menu['foods'] = Menu::getMenuFoods($menu['id']);
            $menu_allergens = [];
            foreach($menu['foods'] as $food) {
                $food_allergens = Food::getAllergens($food['id']);
                $menu_allergens = array_merge($menu_allergens, $food_allergens);
            }
            $menu['allergens'] = array_unique($menu_allergens);
            $menu['allergens_classes'] = implode(' ', array_map('strtolower', array_map('htmlspecialchars', $menu['allergens'])));
        }
        unset($menu);

        $foods = Food::getAll();
        $sorted_foods = Order::sortByType($foods);
        $food_types = Food::getTypes();

        $this->render(
            'menu_editor',
            [
                'menus' => $menus,
                'sorted_foods' => $sorted_foods,
                'food_types' => $food_types
            ]
        );
    }

    public function menuCreator() {
        /*
            INPUT:
                None

            OUTPUT:
                None

            SUMMARY:
                Requires role level 2 or 3, displays a form to create a new menu in the database from the values entered.
        */
        $this->requireRole([2, 3]);
        $this->render('menu_creator');
    }

    public function foodCreator() {
        /*
            INPUT:
                None

            OUTPUT:
                None

            SUMMARY:
                Requires role level 2 or 3, displays a form to create a new food entry in the database from the values entered.
        */
        $this->requireRole([2, 3]);

        require_once __DIR__ . '/../models/Food.php';

        $food_types = Food::getTypes();
        $allergens = Food::getAllAllergens();
        $edit_id = $_GET['edit_id'] ?? null;
        $selected_food_type = $_GET['food_type'] ?? '';
        $edit_mode = false;

        $defaults = [];
        if ($edit_id) {
            $defaults = Food::getById($edit_id);
            if (!$defaults) {
                $_SESSION['error'] = "Erreur : Impossible de trouver ce plat dans la base de données !";
                header('Location: /menu_editor');
                exit();
            }
        } elseif ($selected_food_type) {
            if ($selected_food_type > count($food_types)) {
                $_SESSION['error'] = "Erreur : Le type de nourriture envoyé est invalide !";
                header('Location: /menu_editor');
                exit();
            }
            $defaults['food_type'] = $selected_food_type;
        }

        $this->render(
            'food_creator',
            [
                'edit_id' => $edit_id,
                'food_types' => $food_types,
                'allergens' => $allergens,
                'defaults' => $defaults
            ]
        );
    }

    public static function updateMenu() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }
        if (!isset($_POST['menu_id'])) {
            $_SESSION['error'] = "Erreur : ID du menu manquant lors de la mise à jour.";
            header('Location: /menu_editor');
            exit();
        }

        require_once __DIR__ . '/../models/Menu.php';

        $menu_id = (int)$_POST['menu_id'];

        // Add or remove foods from menu
        if (isset($_POST['action']) && isset($_POST['item_id'])) {
            $food_id = (int)$_POST['item_id'];
            $action = $_POST['action'];

            // Set or increment the amount of foods
            if (isset($_POST['amount']) && $action === 'set') {
                $amount = (int)$_POST['amount'];
                Menu::updateItem($menu_id, $food_id, $action, $amount);
            } else {
                Menu::updateItem($menu_id, $food_id, $action);
            }
        }

        // Change the name of the menu
        if (isset($_POST['name'])) {
            Menu::updateName($menu_id, $_POST['name']);
        }

        // Change the description of the menu
        if (isset($_POST['description'])) {
            Menu::updateDescription($menu_id, $_POST['description']);
        }

        // Change the price of the menu
        if (isset($_POST['price'])) {
            Menu::updatePrice($menu_id, $_POST['price']);
        }

        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            header('Content-Type: application/json');
            $response = [];
            if (isset($_SESSION['error'])) {
                $response['error'] = $_SESSION['error'];
                unset($_SESSION['error']);
            }
            echo json_encode($response);
            exit();
        }

        header("Location: /menu_editor");
        exit();
    }

    public function addMenuFood() {
        require_once __DIR__ . '/../models/Menu.php';

        $this->requireRole([2, 3], true);

        $menu_id = isset($_GET['menu_id']) ? (int)$_GET['menu_id'] : 0;
        $food_id = isset($_GET['food_id']) ? (int)$_GET['food_id'] : 0;

        if ($menu_id > 0 && $food_id > 0) {
            Menu::updateItem($menu_id, $food_id, 'add');
        }

        header('Location: /menu_editor');
        exit;
    }

    public function foodPicker() {
        $this->requireRole([2, 3]);
        $this->render('food_picker', []);
    }

    public function createMenu() {
        require_once __DIR__ . '/../models/Menu.php';

        $this->requireRole([2, 3], true);

        $name = isset($_POST['name']) ? $_POST['name'] : "Menu name";
        $description = isset($_POST['description']) ? $_POST['description'] : "No description";
        $price = isset($_POST['price']) ? $_POST['price'] : 100;

        Menu::createMenu($name, $description, $price);

        header('Location: /menu_editor');
        exit;
    }

    public function manageFood() {
        require_once __DIR__ . '/../models/Food.php';

        $this->requireRole([2, 3], true);

        $edit_id = isset($_POST['edit_id']) ? $_POST['edit_id'] : null;
        $name = isset($_POST['name']) ? $_POST['name'] : "Menu name";
        $food_type = isset($_POST['food_type']) && $_POST['food_type'] != '' ? $_POST['food_type'] : 1;
        $description = isset($_POST['description']) ? $_POST['description'] : "No description";
        $price = isset($_POST['price']) ? $_POST['price'] : 100;
        $image_path = isset($_POST['image_path']) ? $_POST['image_path'] : "/images/unknown.svg";

        if ($edit_id) {
            Food::editFood($edit_id, $name, $food_type, $description, $price, $image_path);
        } else {
            Food::createFood($name, $food_type, $description, $price, $image_path);
        }

        header('Location: /menu_editor');
        exit;
    }
}
