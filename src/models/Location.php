<?php

class Location {
    public static function formAddressHasChanged($target, $form_data) {
        require_once __DIR__ . '/../models/User.php';

        $user_data = User::getUserData($target);
        $location_keys = ['street_nb', 'street_nb_suf', 'street', 'zip_code'];
        $address_has_changed = 0;

        foreach($location_keys as $key)
            if (
                empty($_POST[$key]) != empty($user_data[$key])
                or strtolower($_POST[$key]) != strtolower($user_data[$key])
            )
                $address_has_changed = 1;

        return $address_has_changed;
    }

    public static function getHereApiKey() {
        $PATH = __DIR__ . '/../../conf/here_api_key';
        if (!file_exists($PATH)) return NULL;

        $file = fopen($PATH,'r');
        if (!$file) return NULL;

        $api_key = fgets($file);
        fclose($file);
        return trim($api_key);
    }

    public static function getLocationCoord($location_data) {
        require_once __DIR__ . '/../format_data.php';
        $DEFAULT_COORD = array(
            'lat' => 55.7259517,
            'lng' => 9.1091171
        );

        $api_key = self::getHereApiKey();

        if (empty($api_key))
            return $DEFAULT_COORD;

        $delivery_address = getAddress($location_data);

        $ENDPOINT = 'geocode';
        $baseUrl = 'https://' . $ENDPOINT . '.search.hereapi.com/v1/' . $ENDPOINT;
        $params = [
            'q' => $delivery_address,
            'apiKey' => $api_key
        ];

        $queryString = http_build_query($params, '', '&');
        $url = $baseUrl . '?' . $queryString;
        $response = json_decode(file_get_contents($url), true);

        if (
            empty($response['items'])
            or count($response['items']) < 1
            or empty($response['items'][0]['position'])
        )
            return $DEFAULT_COORD;

        return $response['items'][0]['position'];
    }
}
