<?php


$ar_file = "json/Ar.json";
$jsonData = file_get_contents($ar_file);
$var_lang= json_decode($jsonData, true);


?>