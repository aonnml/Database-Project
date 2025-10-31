<?php
session_start();
header('Content-Type: application/json');
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

include 'config.php';

$user_id = intval($_SESSION['user_id']);
$result = $conn->query("SELECT name, lastname, address, email, phoneNum AS phone, profile_image FROM users WHERE id=$user_id");

if ($result && $user = $result->fetch_assoc()) {
    echo json_encode([
        'name' => $user['name'],
        'lastname' => $user['lastname'],
        'address' => $user['address'],
        'email' => $user['email'],
        'phone' => $user['phone'],
        'profile_image' => !empty($user['profile_image']) ? $user['profile_image'] : 'image/user.jpg'
    ]);
} else {
    echo json_encode(['error' => 'User not found']);
}

$conn->close();
?>
