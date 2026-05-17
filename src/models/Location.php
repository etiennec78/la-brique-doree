<?php

class Location {
    public static function formAddressHasChanged($target, $form_data) {
        require_once __DIR__ . '/../models/User.php';

        $user_data = User::getUserInfo($target);
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

    public static function getAPITimeout($uid) {
        require_once __DIR__ . '/../models/User.php';

        $last_api_call = new DateTime(User::getUserData($uid, 'last_api_call'));
        $delay = 5;
        $last_api_call->add(new DateInterval('PT' . $delay . 'S'));
        $difference = $last_api_call->getTimestamp() - time();
        return $difference;
    }

    public static function getLocationCoord($location_data, $uid) {
        require_once __DIR__ . '/../format_data.php';
        require_once __DIR__ . '/../models/User.php';
        $DEFAULT_COORD = [
            'lat' => 55.7259517,
            'lng' => 9.1091171
        ];

        $api_key = $GLOBALS['config']['here_api_key'] ?? "";

        if (empty($api_key))
            return $DEFAULT_COORD;

        $timeout = self::getAPITimeout($uid);
        if ($timeout > 0) {
            User::incrementSuccessiveAPICalls($uid);

            return ['error' => 'timeout remaining: ' . $timeout . 's'];
        }

        $delivery_address = getAddress($location_data);

        $ENDPOINT = 'geocode';
        $baseUrl = 'https://' . $ENDPOINT . '.search.hereapi.com/v1/' . $ENDPOINT;
        $params = [
            'q' => $delivery_address,
            'apiKey' => $api_key
        ];

        User::setUserData($uid, 'last_api_call', date('Y-m-d H:i:s'));

        $queryString = http_build_query($params, '', '&');
        $url = $baseUrl . '?' . $queryString;
        $response = json_decode(file_get_contents($url), true);

        if (
            empty($response['items'])
            or count($response['items']) < 1
            or empty($response['items'][0]['position'])
        )
            return ['error' => 'Here API did not return valid values'];

        return $response['items'][0]['position'];
    }
}
