<?php
require_once 'config.php';

// รับค่าจากฟอร์ม
$name = $_POST['name'] ?? '';
$lastname = $_POST['lastname'] ?? '';
$address = $_POST['address'] ?? '';
$phoneNum = $_POST['phoneNum'] ?? '';
$email = $_POST['email'] ?? '';
$password_plain = $_POST['password'] ?? '';

// เช็คอีเมลซ้ำ
$check_sql = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "<script>alert('This email is already registered!'); window.history.back();</script>";
    exit;
}

// Hash password
$password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

// บันทึกข้อมูล
$sql = "INSERT INTO users (name, lastname, address, phoneNum, email, password)
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $name, $lastname, $address, $phoneNum, $email, $password_hashed);

if ($stmt->execute()) {
    echo "<script>alert('Register successful!'); window.location.href='../product.html';</script>";
} else {
    echo "Error: " . $stmt->error;
}

$conn->close();
?>
