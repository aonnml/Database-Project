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

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$payload = [];

if (stripos($contentType, 'application/json') !== false) {
    $jsonInput = json_decode(file_get_contents('php://input'), true);
    if (is_array($jsonInput)) {
        $payload = $jsonInput;
    }
} else {
    $payload = $_POST;
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

$currentImagePath = 'image/user.jpg';
$imageStmt = $conn->prepare('SELECT profile_image FROM users WHERE id = ?');

if ($imageStmt) {
    $imageStmt->bind_param('i', $userId);
    $imageStmt->execute();
    $imageStmt->bind_result($existingImage);
    if ($imageStmt->fetch() && !empty($existingImage)) {
        $currentImagePath = $existingImage;
    }
    $imageStmt->close();
}

$profileImagePath = $currentImagePath;

if (!empty($_FILES['profileImage']) && $_FILES['profileImage']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['profileImage']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode([
            'status' => 'error',
            'message' => 'There was an error uploading the file.'
        ]);
        exit;
    }

    $tmpPath = $_FILES['profileImage']['tmp_name'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = $finfo ? finfo_file($finfo, $tmpPath) : null;
    if ($finfo) {
        finfo_close($finfo);
    }

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];

    if (!isset($allowedTypes[$mimeType])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid image type. Please upload JPG, PNG, GIF, or WEBP files.'
        ]);
        exit;
    }

    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    try {
        $randomSuffix = bin2hex(random_bytes(4));
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Unable to process image upload at this time.'
        ]);
        exit;
    }

    $newFileName = $userId . '_' . time() . '_' . $randomSuffix . '.' . $allowedTypes[$mimeType];
    $targetPath = $uploadDir . $newFileName;

    if (!move_uploaded_file($tmpPath, $targetPath)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to save uploaded image.'
        ]);
        exit;
    }

    if ($currentImagePath && strpos($currentImagePath, 'uploads/') === 0) {
        $oldImagePath = __DIR__ . '/../' . $currentImagePath;
        if (is_file($oldImagePath)) {
            @unlink($oldImagePath);
        }
    }

    $profileImagePath = 'uploads/' . $newFileName;
}

if (!$profileImagePath) {
    $profileImagePath = 'image/user.jpg';
}

$sql = "UPDATE users
        SET name = ?, lastname = ?, address = ?, phoneNum = ?, email = ?, profile_image = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error'
    ]);
    exit;
}

$stmt->bind_param('ssssssi', $name, $lastname, $address, $phone, $email, $profileImagePath, $userId);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'profile_image' => $profileImagePath
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