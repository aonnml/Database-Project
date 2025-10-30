<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not logged in'
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

$sql = "
SELECT c.id,
       p.name,
       p.price,
       p.image,
       c.quantity
FROM cart c
INNER JOIN product p ON c.productId = p.id
WHERE c.userId = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

$items = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'status' => 'success',
    'data' => $items
]);

$stmt->close();
$conn->close();
