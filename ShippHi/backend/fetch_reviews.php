<?php
include 'config.php';
session_start();

$userId = $_SESSION['user_id'];

$sql = "SELECT r.*, p.name AS product_name, p.image AS product_image
        FROM review r
        JOIN product p ON r.productId = p.id
        WHERE r.userId = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

echo json_encode($reviews);
?>
