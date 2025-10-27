<?php
include('config.php');
header('Content-Type: application/json');

$result = $conn->query("SELECT id, name, price, size FROM product");

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
$conn->close();
?>
