<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true) ?? [];
$cartIds = $payload['cartIds'] ?? [];

if (!is_array($cartIds) || count($cartIds) === 0) {
    echo json_encode(['success' => false, 'error' => 'ไม่พบสินค้าที่ต้องชำระ']);
    exit;
}

$cartIds = array_map('intval', $cartIds);
$cartIds = array_filter($cartIds, function ($id) {
    return $id > 0;
});
$cartIds = array_values(array_unique($cartIds));

if (count($cartIds) === 0) {
    echo json_encode(['success' => false, 'error' => 'ไม่พบสินค้าที่ต้องชำระ']);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$idsList = implode(',', $cartIds);

$conn->begin_transaction();

try {
    $selectSql = "SELECT id, productId FROM cart WHERE userId = ? AND id IN ($idsList) FOR UPDATE";
    $selectStmt = $conn->prepare($selectSql);

    if (!$selectStmt) {
        throw new Exception('Database error');
    }

    $selectStmt->bind_param('i', $userId);
    $selectStmt->execute();
    $result = $selectStmt->get_result();

    $selectedItems = [];
    while ($row = $result->fetch_assoc()) {
        $selectedItems[] = $row;
    }

    $selectStmt->close();

    if (count($selectedItems) === 0) {
        throw new Exception('ไม่พบสินค้าที่เลือกไว้ในตะกร้า');
    }

    $insertStmt = $conn->prepare("INSERT INTO review (userId, productId, rate, description, is_reviewed) VALUES (?, ?, 0, '', 0)");
    $checkStmt = $conn->prepare("SELECT id FROM review WHERE userId = ? AND productId = ? AND is_reviewed = 0 LIMIT 1");

    if (!$insertStmt || !$checkStmt) {
        throw new Exception('Database error');
    }

    foreach ($selectedItems as $item) {
        $productId = (int) $item['productId'];
        if ($productId <= 0) {
            continue;
        }

        $checkStmt->bind_param('ii', $userId, $productId);
        if (!$checkStmt->execute()) {
            throw new Exception('Database error');
        }
        $checkStmt->store_result();

        if ($checkStmt->num_rows === 0) {
            $insertStmt->bind_param('ii', $userId, $productId);
            if (!$insertStmt->execute()) {
                throw new Exception('Database error');
            }
        }

        $checkStmt->free_result();
    }

    $deleteSql = "DELETE FROM cart WHERE userId = ? AND id IN ($idsList)";
    $deleteStmt = $conn->prepare($deleteSql);

    if (!$deleteStmt) {
        throw new Exception('Database error');
    }

    $deleteStmt->bind_param('i', $userId);
    if (!$deleteStmt->execute()) {
        throw new Exception('Database error');
    }

    $conn->commit();

    echo json_encode([
        'success' => true,
        'removedCartIds' => array_column($selectedItems, 'id')
    ]);
} catch (Throwable $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'error' => 'ไม่สามารถทำรายการได้ กรุณาลองใหม่'
    ]);
}

if (isset($insertStmt) && $insertStmt instanceof mysqli_stmt) {
    $insertStmt->close();
}
if (isset($checkStmt) && $checkStmt instanceof mysqli_stmt) {
    $checkStmt->close();
}
if (isset($deleteStmt) && $deleteStmt instanceof mysqli_stmt) {
    $deleteStmt->close();
}

$conn->close();
?>
