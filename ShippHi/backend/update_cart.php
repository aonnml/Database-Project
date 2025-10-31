<?php
header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not logged in'
    ]);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true) ?? [];
$cartId = isset($payload['cart_id']) ? (int)$payload['cart_id'] : 0;
$quantity = isset($payload['quantity']) ? (int)$payload['quantity'] : 0;

if ($cartId <= 0 || $quantity <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid input'
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

$sql = "UPDATE cart SET quantity = ? WHERE id = ? AND userId = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error'
    ]);
    exit;
}

$stmt->bind_param('iii', $quantity, $cartId, $userId);
$conn->begin_transaction();

try {
    if (!$stmt->execute()) {
        throw new Exception('execute failed');
    }

    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'quantity' => $quantity
    ]);
} catch (Throwable $e) {
    $conn->rollback();

    echo json_encode([
        'status' => 'error',
        'message' => 'Could not update quantity'
    ]);
}

$stmt->close();
$conn->close();
?>