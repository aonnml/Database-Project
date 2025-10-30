<?php
header('Content-Type: application/json; charset=utf-8');

require_once "config.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'error' => 'User not logged in'
    ]);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true) ?? [];
$selectedIds = $payload['cartIds'] ?? [];

if (!is_array($selectedIds) || count($selectedIds) === 0) {
    echo json_encode([
        'cartItems' => [],
        'totalPrice' => 0
    ]);
    exit;
}

$selectedIds = array_values(array_filter(array_map('intval', $selectedIds), fn($id) => $id > 0));

if (count($selectedIds) === 0) {
    echo json_encode([
        'cartItems' => [],
        'totalPrice' => 0
    ]);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$placeholders = implode(',', array_fill(0, count($selectedIds), '?'));

$sql = "SELECT c.id, p.name, p.price, c.quantity, (p.price * c.quantity) AS subtotal
        FROM cart c
        JOIN product p ON c.productId = p.id
        WHERE c.userId = ? AND c.id IN ($placeholders)";

$types = str_repeat('i', count($selectedIds) + 1);
$params = array_merge([$userId], $selectedIds);

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'error' => 'Database error'
    ]);
    exit;
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
$totalPrice = 0;

while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
    $totalPrice += (float) $row['subtotal'];
}

echo json_encode([
    'cartItems' => $cartItems,
    'totalPrice' => $totalPrice
]);

$stmt->close();
$conn->close();
