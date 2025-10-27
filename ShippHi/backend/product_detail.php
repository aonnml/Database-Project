<?php
header('Content-Type: application/json');
require_once 'config.php';

$id = intval($_GET['id'] ?? 0);

$sql = "
SELECT p.*, c.name AS category
FROM product p
LEFT JOIN category c ON p.categoryId = c.id
WHERE p.id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    echo json_encode(["error" => "Product not found"]);
}
$conn->close();
?>
