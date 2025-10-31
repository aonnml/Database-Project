<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'error';
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$rate = isset($_POST['rate']) ? (float) $_POST['rate'] : 0.0;
$desc = trim($_POST['description'] ?? '');

if ($id <= 0 || $rate <= 0) {
    echo 'error';
    exit;
}

$conn->begin_transaction();

try {
    $sql = "UPDATE review SET rate = ?, description = ?, is_reviewed = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception('prepare failed');
    }

    $stmt->bind_param('dsi', $rate, $desc, $id);

    if (!$stmt->execute()) {
        throw new Exception('execute failed');
    }

    $stmt->close();

    $conn->commit();
    echo 'success';
} catch (Throwable $e) {
    $conn->rollback();
    echo 'error';
}

$conn->close();
?>
