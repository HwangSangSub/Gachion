#!/usr/bin/php
<?php
echo "argv params: ";
print_r($argv); 
if ($argv[1]) {echo "got the size right, wilbur!  argv element 1: ".$argv[1];}

$query_array = array();

parse_str($_SERVER['QUERY_STRING'], $query_array);

print_r($query_array);
?>