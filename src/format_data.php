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
    $street_part = concatenate($address_dict, ['street_nb', 'street_nb_suf', 'street'], ' ');
    $city_part = concatenate($address_dict, ['zip_code', 'town'], ' ');
    
    $parts = [];
    if (!empty($street_part)) $parts[] = $street_part;
    if (!empty($city_part)) $parts[] = $city_part;
    
    return implode(', ', $parts);
}
