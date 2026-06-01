<?php
require_once __DIR__ . '/../db_connect.php';

class Order {
    public static function getOrderById($id) {
  /*

    INPUT :

         (int) $id : variable representing the order ID

    OUTPUT :

      (array|bool) $order : variable representing the full order data record, or false if not found


    SUMMARY :

    This function fetches a single order record from the database using its unique order ID.

  */
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function getOrdersFromState($order_names) {
  /*

    INPUT :

         (array) $order_names : variable representing an array of order status names to filter by

    OUTPUT :

      (array) $orders : variable representing a list of orders matching the specified statuses, sorted by takeaway time and ID


    SUMMARY :

    This function retrieves a list of orders that match any of the provided status names, complete with customer and delivery person names, ordered chronologically.

  */
        global $pdo;
        $placeholders = implode(',', array_fill(0, count($order_names), '?'));
        $stmt = $pdo->prepare("
            SELECT 
                o.id, 
                u.first_name, 
                u.last_name, 
                o.is_takeaway, 
                o.takeaway_time,
                deliv.first_name AS delivery_first_name,
                deliv.last_name AS delivery_last_name
            FROM orders o
            JOIN users u ON o.user_id = u.id
            JOIN order_status os ON o.order_status_id = os.id
            LEFT JOIN users deliv ON o.delivery_person_id = deliv.id
            WHERE os.name IN ($placeholders)
            ORDER BY COALESCE(o.takeaway_time, '1000-01-01 00:00:00') ASC, o.id ASC
        ");
        $stmt->execute($order_names);
        return $stmt->fetchAll();
    }

    public static function getAllCompletedOrderIdsFromUser($uid) {
  /*

    INPUT :

         (int) $uid : variable representing the user ID

    OUTPUT :

      (array) $ids : variable representing a sequential array of completed order IDs (status ID 5)


    SUMMARY :

    This function fetches a list of IDs for all completed orders belonging to a specific user.

  */
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT o.id
            FROM orders o
            WHERE o.user_id = ? AND o.order_status_id = 5
            ORDER BY o.id DESC
        ");
        $stmt->execute([$uid]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function getAvailableStaff($role_name) {
  /*

    INPUT :

         (str) $role_name : variable representing the role name ('cook' or 'delivery_person')

    OUTPUT :

      (int|null) $staff_id : variable representing the ID of an available staff member, or null if none are available


    SUMMARY :

    This function identifies an available, non-busy staff member for a given role who was assigned an order longest ago.

  */
        global $pdo;

        $allowed_roles = ['cook', 'delivery_person'];
        if (!in_array($role_name, $allowed_roles, true)) {
            return null;
        }

        // Return false if the staff member has an order with this status
        $busy_status_map = [
            'cook' => 2, // 2 = preparing
            'delivery_person' => 4 // 4 = shipping
        ];
        $busy_status_id = $busy_status_map[$role_name];

        // Get the staff member assigned a long time ago, that is currently not busy
        $stmt = $pdo->prepare("
            SELECT u.id
            FROM users u
            JOIN role r ON u.role_id = r.id
            LEFT JOIN orders o ON o.{$role_name}_id = u.id
            WHERE r.name = ?
            AND NOT EXISTS (
                SELECT 1
                FROM orders active_o
                WHERE active_o.{$role_name}_id = u.id
                AND active_o.order_status_id = ?
            )
            GROUP BY u.id
            ORDER BY MAX(o.{$role_name}_assigned_at) ASC
            LIMIT 1
        ");
        $stmt->execute([$role_name, $busy_status_id]);

        $staff_id = $stmt->fetch(PDO::FETCH_COLUMN);
        return $staff_id !== false ? $staff_id : null; // Return null if false
    }

    public static function setShippingStatus($order_id, $delivery_person_id) {
  /*

    INPUT :

         (int) $order_id : variable representing the order ID
     (int) $delivery_person_id : variable representing the delivery person ID

    OUTPUT :

      None


    SUMMARY :

    This function updates an order's status to 'shipping' (status ID 4) and assigns a specific delivery person along with the current timestamp.

  */
        global $pdo;
        $stmt = $pdo->prepare("
            UPDATE orders
            SET
                delivery_person_id = ?,
                order_status_id = 4,
                delivery_person_assigned_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$delivery_person_id, $order_id]);
    }

    public static function setReadyStatus($order_id) {
  /*

    INPUT :

         (int) $order_id : variable representing the order ID

    OUTPUT :

      None


    SUMMARY :

    This function updates an order's status to 'ready' (status ID 3).

  */
        global $pdo;
        $stmt = $pdo->prepare("
            UPDATE orders
            SET order_status_id = 3
            WHERE id = ?
        ");
        $stmt->execute([$order_id]);
    }

    public static function setDeliveredStatus($order_id) {
  /*

    INPUT :

         (int) $order_id : variable representing the order ID

    OUTPUT :

      None


    SUMMARY :

    This function updates an order's status to 'delivered' (status ID 5).

  */
        global $pdo;
        $stmt = $pdo->prepare("
        UPDATE orders
        SET order_status_id = 5
        WHERE id = ?
        ");
        $stmt->execute([$order_id]);
    }

    public static function cancelDelivery($order_id, $uid) {
  /*

    INPUT :

         (int) $order_id : variable representing the order ID
     (int) $uid : variable representing the delivery person ID

    OUTPUT :

      None


    SUMMARY :

    This function logs a delivery cancellation record and resets the order status back to 'ready' (status ID 3) while clearing the delivery person assignment.

  */
        global $pdo;
        
        $stmt_cancel = $pdo->prepare("
            INSERT IGNORE INTO delivery_cancellation (order_id, delivery_person_id)
            VALUES (?, ?)
        ");
        $stmt_cancel->execute([$order_id, $uid]);
        
        $stmt = $pdo->prepare("
            UPDATE orders
            SET order_status_id = 3,
                delivery_person_id = NULL,
                delivery_person_assigned_at = NULL
            WHERE id = ?
        ");
        $stmt->execute([$order_id]);
    }

    public static function getLastOrder($uid) {
  /*

    INPUT :

         (int) $uid : variable representing the user ID

    OUTPUT :

      (array|bool) $order : variable representing the latest order data including its review ID, or false if not found


    SUMMARY :

    This function retrieves the most recent order placed by a customer, along with any associated review ID.

  */
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT o.id as order_id, r.id as review_id, o.is_takeaway
            FROM orders o
            LEFT JOIN reviews r ON r.order_id = o.id
            WHERE o.user_id = ?
            ORDER BY o.id DESC
            LIMIT 1
        ");
        $stmt->execute([$uid]);
        return $stmt->fetch();
    }


    public static function checkOrderExistsByCartId($cart_id) {
  /*

    INPUT :

         (int) $cart_id : variable representing the cart ID

    OUTPUT :

      (array|bool) $order : variable representing the order record matching the cart ID, or false if not found


    SUMMARY :

    This function checks if an order has already been created for a given cart ID.

  */
        global $pdo;
        $stmt = $pdo->prepare("SELECT id FROM orders WHERE cart_id = ?");
        $stmt->execute([$cart_id]);
        return $stmt->fetch();
    }

    public static function createOrder($cart_id, $user_id, $order_status_id, $cook_id, $is_takeaway, $takeaway_time) {
  /*

    INPUT :

         (int) $cart_id : variable representing the cart ID
     (int) $user_id : variable representing the customer user ID
     (int|null) $cook_id : variable representing the cook user ID
     (int) $order_status_id : variable representing the order status ID
     (int|bool) $is_takeaway : variable representing whether the order is takeaway
     (str|null) $takeaway_time : variable representing the requested takeaway time

    OUTPUT :

      (string) $order_id : variable representing the newly created order ID


    SUMMARY :

    This function inserts a new order record into the database with its initial details and returns its ID.

  */
        global $pdo;
        $cook_assigned_at = $cook_id == null ? "NULL" : "NOW()";

        $stmt = $pdo->prepare("
            INSERT INTO orders (cart_id, user_id, cook_id, order_status_id, is_takeaway, takeaway_time, cook_assigned_at)
            VALUES (?, ?, ?, ?, ?, ?, $cook_assigned_at)
        ");
        $stmt->execute([$cart_id, $user_id, $cook_id, $order_status_id, $is_takeaway, $takeaway_time]);
        return $pdo->lastInsertId();
    }

    public static function deliveryCanceled($order_id, $uid) {
  /*

    INPUT :

         (int) $order_id : variable representing the order ID
     (int) $uid : variable representing the delivery person ID

    OUTPUT :

      (bool) $is_canceled : variable representing whether the order delivery was canceled by this delivery person


    SUMMARY :

    This function checks if a delivery person has previously canceled the delivery for a specific order.

  */
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT o.* FROM orders o
            JOIN delivery_cancellation dc ON dc.order_id = o.id AND dc.delivery_person_id = ?
            WHERE o.id = ? AND o.order_status_id = 3 AND o.is_takeaway = 0
        ");
        $stmt->execute([$uid, $order_id]);
        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getNextDeliveries($uid, $limit = 3) {
  /*

    INPUT :

         (int) $uid : variable representing the delivery person ID
     (int) $limit : variable representing the maximum number of deliveries to fetch (defaults to 3)

    OUTPUT :

      (array) $orders : variable representing a list of available orders ready for delivery


    SUMMARY :

    This function fetches the next available orders that are ready for delivery and have not been canceled by the specifying delivery person.

  */
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT o.* FROM orders o
            LEFT JOIN delivery_cancellation dc ON dc.order_id = o.id AND dc.delivery_person_id = ?
            WHERE o.order_status_id = 3 AND o.is_takeaway = 0 AND dc.order_id IS NULL
            ORDER BY o.id ASC
            LIMIT " . (int)$limit . "
        ");
        $stmt->execute([$uid]);
        return $stmt->fetchAll();
    }

    public static function getUserActiveOrders($uid, $force_ids = []) {
  /*

    INPUT :

         (int) $uid : variable representing the user ID
     (array) $force_ids : variable representing an optional list of order IDs to explicitly include

    OUTPUT :

      (array) $orders : variable representing an array of active order records


    SUMMARY :

    This function retrieves all active or in-progress orders for a user, with the ability to forcefully include specific order IDs.

  */
        global $pdo;
        $params = [$uid];
        $force_sql = "";
        
        if (!empty($force_ids)) {
            $inQuery = implode(',', array_fill(0, count($force_ids), '?'));
            $force_sql = " OR o.id IN ($inQuery)";
            $params = array_merge($params, $force_ids);
        }

        $stmt = $pdo->prepare("
            SELECT o.id, os.id as status, o.cook_id, o.delivery_person_id, o.is_takeaway
            FROM order_status os
            JOIN orders o on o.order_status_id = os.id
            WHERE o.user_id = ? AND (os.id < 5 $force_sql)
            ORDER BY o.id DESC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getOrderStatuses($uid, $order_ids) {
  /*

    INPUT :

         (int) $uid : variable representing the user ID
     (array) $order_ids : variable representing an array of order IDs to query

    OUTPUT :

      (array) $statuses : variable representing an associative array mapping order IDs to their status IDs


    SUMMARY :

    This function retrieves the status IDs for a collection of order IDs belonging to a specific customer.

  */
        global $pdo;
        $inQuery = implode(',', array_fill(0, count($order_ids), '?'));
        $stmt = $pdo->prepare("
            SELECT o.id, os.id as status
            FROM order_status os
            JOIN orders o on o.order_status_id = os.id
            WHERE o.user_id = ? AND o.id IN ($inQuery)
        ");
        $params = array_merge([$uid], $order_ids);
        $stmt->execute($params);
        $result = [];
        foreach($stmt->fetchAll() as $row) {
             $result[$row['id']] = $row['status'];
        }
        return $result;
    }
    
    public static function getOrderItems($order_id) {
  /*

    INPUT :

         (int) $order_id : variable representing the order ID

    OUTPUT :

      (array) $items : variable representing an associative array containing separate lists of foods and menus


    SUMMARY :

    This function retrieves all food and menu items associated with a given order through its linked cart.

  */
        global $pdo;

        $stmt = $pdo->prepare("
            SELECT f.name, cf.quantity
            FROM orders o
            JOIN cart_food cf ON cf.cart_id = o.cart_id
            JOIN food f ON f.id = cf.food_id
            WHERE o.id = ?
        ");
        $stmt->execute([$order_id]);
        $foods = $stmt->fetchAll();

        $stmt = $pdo->prepare("
            SELECT m.name, cm.quantity
            FROM orders o
            JOIN cart_menu cm ON cm.cart_id = o.cart_id
            JOIN menu m ON m.id = cm.menu_id
            WHERE o.id = ?
        ");
        $stmt->execute([$order_id]);
        $menus = $stmt->fetchAll();

        return ['foods' => $foods, 'menus' => $menus];
    }

    public static function sortByType($foods) {
  /*

    INPUT :

         (array) $foods : variable representing an array of food items

    OUTPUT :

      (array) $sorted_foods : variable representing an associative array of food items grouped by their food type


    SUMMARY :

    This function categorizes and groups a collection of food items into a dictionary structure using their food type as keys.

  */
        // Sort a list of foods in a dictionnary with food types as keys
        $dict = [];

        foreach ($foods as $food) {
            $type = $food['food_type'];

            if (!isset($dict[$type])) {
                $dict[$type] = [];
            }
            $dict[$type][] = $food;
        }

        return $dict;
    }

    public static function setOrderCookAsAdmin($order_id) {
  /*

    INPUT :

         (int) $order_id : variable representing an order ID

    OUTPUT :

      none


    SUMMARY :

    This function sets the cook ID of a specific order to NULL, effectively setting it as an admin order in order history.

  */
        global $pdo;
        $stmt = $pdo->prepare("
            UPDATE orders
            SET cook_id = NULL
            WHERE id = ?
        ");
        $stmt->execute([$order_id]);
    }
}
