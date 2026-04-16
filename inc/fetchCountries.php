<?php
include 'conn.php'; // Path to your DB connection script

$query = "SELECT CountryID, Country$lang AS CountryName FROM Countries";
$result = $conn->query($query);

$countries = [];
while ($row = $result->fetch_assoc()) {
    $countries[] = $row;
}

header('Content-Type: application/json');
echo json_encode($countries);

