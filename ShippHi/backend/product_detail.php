<?php
include('config.php');
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Missing product ID']);
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT p.id, p.name, p.price, p.size, c.name AS category
        FROM product p
        LEFT JOIN category c ON p.categoryId = c.id
        WHERE p.id = $id";

$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    echo json_encode(['error' => 'Product not found']);
}

$conn->close();
?>
