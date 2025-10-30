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

$payload = json_decode(file_get_contents('php://input'), true);

if (!$payload || !is_array($payload)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid payload'
    ]);
    exit;
}

$name = trim($payload['name'] ?? '');
$lastname = trim($payload['lastname'] ?? '');
$address = trim($payload['address'] ?? '');
$phone = trim($payload['phone'] ?? '');
$email = trim($payload['email'] ?? '');

if ($name === '' || $lastname === '' || $email === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Name, last name, and email are required.'
    ]);
    exit;
}

$userId = (int) $_SESSION['user_id'];

$sql = "UPDATE users
        SET name = ?, lastname = ?, address = ?, phoneNum = ?, email = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error'
    ]);
    exit;
}

$stmt->bind_param('sssssi', $name, $lastname, $address, $phone, $email, $userId);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update profile.'
    ]);
}

$stmt->close();
$conn->close();
?>