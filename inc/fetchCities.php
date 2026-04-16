<?php
include 'conn.php'; // Path to your DB connection script


$countryID = isset($_GET['countryID']) ? intval($_GET['countryID']) : 0;

$query = "SELECT CityID, City$lang AS CityName FROM Cities WHERE CountryID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $countryID);
$stmt->execute();
$result = $stmt->get_result();

$cities = [];
while ($row = $result->fetch_assoc()) {
    $cities[] = $row;
}

header('Content-Type: application/json');
echo json_encode($cities);