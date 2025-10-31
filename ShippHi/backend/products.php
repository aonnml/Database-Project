<?php
header('Content-Type: application/json');
require_once 'config.php';

$sql = "
SELECT
    p.id,
    p.name,
    p.price,
    p.size,
    p.image,
    c.name AS category,
    COALESCE(ROUND(AVG(CASE WHEN r.is_reviewed = 1 AND r.rate > 0 THEN r.rate END), 2), 0) AS average_rating,
    COALESCE(SUM(CASE WHEN r.is_reviewed = 1 AND r.rate > 0 THEN 1 ELSE 0 END), 0) AS review_count
FROM product p
LEFT JOIN category c ON p.categoryId = c.id
LEFT JOIN review r ON r.productId = p.id
GROUP BY p.id, p.name, p.price, p.size, p.image, c.name
";

$result = $conn->query($sql);
$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
$conn->close();
?>
