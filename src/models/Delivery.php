<?php
require_once __DIR__ . '/../db_connect.php';

class Delivery {
    public static function getDeliveries($uid) {
  /*

    INPUT :

         (int) $uid : variable representing the delivery person user ID

    OUTPUT :

      (array) $deliveries : variable representing an array of active deliveries for the user


    SUMMARY :

    This function retrieves a list of all active shipping orders and customer details assigned to a specific delivery person.

  */
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT o.id, u.first_name, u.last_name, u.street_nb, u.street_nb_suf, u.street, u.town, u.zip_code, u.intercom_code, u.latitude, u.longitude
            FROM orders o
            LEFT JOIN users u ON o.customer_id = u.id
            JOIN order_status os ON os.id = o.order_status_id
            WHERE o.delivery_person_id = ? AND os.name = 'shipping'
        ");
        $stmt->execute([$uid]);
        return $stmt->fetchAll();
    }

    public static function getDeliveriesCoordinates($delivery_person_id) {
  /*

    INPUT :

         (int) $delivery_person_id : variable representing the delivery person user ID

    OUTPUT :

      (array) $coordinates : variable representing an array containing the latitude and longitude coordinates


    SUMMARY :

    This function fetches the geographical coordinates of the customers for active shipping orders assigned to a specific delivery person.

  */
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT u.latitude, u.longitude
            FROM orders o
            JOIN users u ON o.customer_id = u.id
            JOIN order_status os ON os.id = o.order_status_id
            WHERE o.delivery_person_id = ? AND os.name = 'shipping'
        ");
        $stmt->execute([$delivery_person_id]);
        return $stmt->fetchAll();
    }

    public static function buildMapURL($coordinates) {
  /*

    INPUT :

         (array) $coordinates : variable representing an array of coordinates containing latitude and longitude

    OUTPUT :

      (str) $url : variable representing the generated OpenStreetMap embed URL


    SUMMARY :

    This function constructs a map URL from OpenStreetMap using the provided geographical coordinates and bounding box settings.

  */
        $baseUrl = "https://www.openstreetmap.org/export/embed.html";
        $params = [
            "bbox" => implode(',', [9.113277196884157, 55.72994659971866, 9.11605328321457, 55.73135269752343]),
            "layer" => "mapnik"
        ];

        if (!empty($coordinates)) {
            $params['marker'] = $coordinates[0]['latitude'] . ',' . $coordinates[0]['longitude'];
        }

        $queryString = http_build_query($params, '', '&');
        $url = $baseUrl . '?' . urldecode($queryString);

        return $url;
    }
}
