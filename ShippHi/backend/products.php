<?php
header('Content-Type: application/json');
require_once 'config.php';

$sql = "
SELECT p.id, p.name, p.price, p.size, p.image, c.name AS category
FROM product p
LEFT JOIN category c ON p.categoryId = c.id
";

$result = $conn->query($sql);
$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
$conn->close();
?>
