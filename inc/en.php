<?php


$en_file = "json/En.json";
$jsonData = file_get_contents($en_file);
$var_lang= json_decode($jsonData, true);


?>