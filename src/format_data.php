<?php
function concatenate($dict, $values, $separator = ' ') {
    /*
        
     INPUT :
             
        (array) $dict : variable representing the source data dictionary
        (array) $values : variable representing the array of keys to extract and combine
        (string) $separator : variable representing the string delimiter used to join elements
      
     OUTPUT :

        (string) : variable representing the joined string result

      
     SUMMARY :
        
        Loops through a list of specified dictionary keys, aggregates non-empty corresponding values into an internal array structure, and returns them concatenated by a custom string separator.

    */
    $result = [];
    foreach ($values as $val) {
        if (!empty($dict[$val])) {
            $result[] = $dict[$val];
        }
    }
    return implode($separator, $result);
}

function getName($name_dict) {
    /*
        
     INPUT :
             
        (array) $name_dict : variable representing the dictionary containing user name segments
      
     OUTPUT :

        (string) : variable representing the fully combined name string

      
     SUMMARY :
        
        Leverages the generic string concatenation helper to join the first name and last name attributes from a given dictionary separated by a space character.

    */
    return concatenate($name_dict, ['first_name', 'last_name'], ' ');
}

function getAddress($address_dict) {
    /*
        
     INPUT :
             
        (array) $address_dict : variable representing the dictionary containing structural location descriptors
      
     OUTPUT :

        (string) : variable representing the fully formatted human-readable address string

      
     SUMMARY :
        
        Evaluates and processes street numbers, street names, postal codes, and city fields while avoiding duplicates to assemble a cleanly formatted comma-separated address string.

    */
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
