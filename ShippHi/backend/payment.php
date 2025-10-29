<?php
include "config.php";
session_start();

$userId = $_SESSION['user_id'];

$sql = "SELECT c.id, p.name, p.price, c.quantity, (p.price * c.quantity) AS subtotal
        FROM cart c
        JOIN product p ON c.productId = p.id
        WHERE c.userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
$totalPrice = 0;
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
    $totalPrice += $row['subtotal'];
}

echo json_encode([
    "cartItems" => $cartItems,
    "totalPrice" => $totalPrice
]);
