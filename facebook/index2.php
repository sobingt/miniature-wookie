<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

$string1="Accenture" ;
$string2= "Accenture Services Pvt Ltd";

function unique_chars($string) {
   return count_chars(strtolower(str_replace(' ', '', $string)), 3);
}
function compare_strings($a, $b) {
    $index = similar_text(unique_chars($a), unique_chars($b), $percent);
    return array('index' => $index, 'percent' => $percent);
}
print_r( compare_strings($string1, $string2) );

// outputs:


?>