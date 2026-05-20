<?php
function concatenate($dict, $values, $separator = ' ') {
    $result = [];
    foreach ($values as $val) {
        if (!empty($dict[$val])) {
            $result[] = $dict[$val];
        }
    }
    return implode($separator, $result);
}

function getName($name_dict) {
    return concatenate($name_dict, ['first_name', 'last_name'], ' ');
}

function getAddress($address_dict) {
    $street = !empty($address_dict['street']) ? $address_dict['street'] : '';
    $street_nb = !empty($address_dict['street_nb']) ? $address_dict['street_nb'] : '';

    if (!empty($street_nb) && strpos($street, $street_nb) === 0) { // Check if street already starts with the street number to avoid duplicates
        $street_part = concatenate($address_dict, ['street_nb_suf', 'street'], ' ');
    } else {
        $street_part = concatenate($address_dict, ['street_nb', 'street_nb_suf', 'street'], ' ');
    }
    $city_part = concatenate($address_dict, ['zip_code', 'town'], ' ');
    
    $parts = [];
    if (!empty($street_part)) $parts[] = $street_part;
    if (!empty($city_part)) $parts[] = $city_part;
    
    return implode(', ', $parts);
}
