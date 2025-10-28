<?php
require_once 'config.php';
session_start();

$userId = $_SESSION['user_id'] ?? 1; // ถ้ายังไม่ทำระบบ Login ใช้ mock id = 1
$productId = $_POST['productId'] ?? 0;
$quantity = $_POST['quantity'] ?? 1;

if (!$productId) {
    die("❌ Invalid product ID");
}

$sql = "INSERT INTO cart (userId, productId, quantity)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $userId, $productId, $quantity);

if ($stmt->execute()) {
    echo "✅ Added to cart!";
} else {
    echo "❌ Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
