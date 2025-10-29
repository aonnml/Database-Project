<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];

$conn->begin_transaction();

try {
    $cartResult = $conn->prepare("SELECT productId FROM cart WHERE userId = ?");
    $cartResult->bind_param("i", $userId);
    $cartResult->execute();
    $res = $cartResult->get_result();
    $cartItems = $res->fetch_all(MYSQLI_ASSOC);

    if (empty($cartItems)) {
        echo json_encode(['success' => false, 'error' => 'ตะกร้าว่าง']);
        exit;
    }

    $stmtInsert = $conn->prepare("
        INSERT INTO review (userId, productId, rate, description, is_reviewed)
        VALUES (?, ?, 0, '', 0)
    ");

    foreach ($cartItems as $item) {
        $productId = $item['productId'];
        $stmtInsert->bind_param("ii", $userId, $productId);
        $stmtInsert->execute();
    }

    $stmtDelete = $conn->prepare("DELETE FROM cart WHERE userId = ?");
    $stmtDelete->bind_param("i", $userId);
    $stmtDelete->execute();

    $conn->commit();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
