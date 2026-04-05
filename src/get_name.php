<?php
function getName($name_dict)
{
    $name = '';
    $first_name = isset($name_dict['first_name']);
    $last_name = isset($name_dict['last_name']);
    if ($first_name) {
        $name = $name . $name_dict['first_name'];
        if ($last_name)
            $name = $name . ' ';
    }
    if ($last_name)
        $name = $name . $name_dict['last_name'];
    return $name;
}
?>
