<?php
require_once 'config.php';

$name = $_POST['name'] ?? '';
$categoryId = $_POST['categoryId'] ?? null;
$description = $_POST['description'] ?? '';
$price = $_POST['price'] ?? 0;
$stock = $_POST['stock'] ?? 0;
$size = $_POST['size'] ?? '';
$imagePath = '';

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = "../uploads/";
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
    $imageName = time() . "_" . basename($_FILES['image']['name']);
    $targetFile = $uploadDir . $imageName;
    move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
    $imagePath = "uploads/" . $imageName;
}

$sql = "INSERT INTO product (categoryId, name, description, price, stock, size, image)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("issdiis", $categoryId, $name, $description, $price, $stock, $size, $imagePath);

if ($stmt->execute()) {
    echo "<script>alert('Product added successfully!'); window.location.href='../product.html';</script>";
} else {
    echo "Error: " . $stmt->error;
}

$conn->close();
?>
