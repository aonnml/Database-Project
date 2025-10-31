<?php
header('Content-Type: application/json');
require_once 'config.php';

$id = intval($_GET['id'] ?? 0);

$sql = "
SELECT
    p.id,
    p.categoryId,
    p.name,
    p.price,
    p.size,
    p.description,
    p.stock,
    p.image,
    c.name AS category,
    COALESCE(ROUND(AVG(CASE WHEN r.is_reviewed = 1 AND r.rate > 0 THEN r.rate END), 2), 0) AS average_rating,
    COALESCE(SUM(CASE WHEN r.is_reviewed = 1 AND r.rate > 0 THEN 1 ELSE 0 END), 0) AS review_count
FROM product p
LEFT JOIN category c ON p.categoryId = c.id
LEFT JOIN review r ON r.productId = p.id
WHERE p.id = ?
GROUP BY p.id, p.categoryId, p.name, p.price, p.size, p.description, p.stock, p.image, c.name
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if (!$row = $result->fetch_assoc()) {
    echo json_encode(["error" => "Product not found"]);
    $stmt->close();
    $conn->close();
    exit;
}

$stmt->close();

$reviewsSql = "
SELECT
    r.id,
    r.rate,
    r.description,
    r.review_date,
    COALESCE(u.name, 'Anonymous') AS reviewer_name,
    COALESCE(u.lastname, '') AS reviewer_lastname
FROM review r
LEFT JOIN users u ON u.id = r.userId
WHERE r.productId = ? AND r.is_reviewed = 1 AND r.rate > 0
ORDER BY r.review_date DESC
";

$reviewsStmt = $conn->prepare($reviewsSql);
$reviewsStmt->bind_param("i", $id);
$reviewsStmt->execute();
$reviewsResult = $reviewsStmt->get_result();

$reviews = [];
while ($review = $reviewsResult->fetch_assoc()) {
    $reviews[] = $review;
}

$reviewsStmt->close();
$conn->close();

$row['reviews'] = $reviews;

echo json_encode($row);
?>
