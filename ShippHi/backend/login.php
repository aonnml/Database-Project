<?php
$servername = "db";
$username = "admin";
$password = "admin1234";
$dbname = "shipphi";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];
$password_input = $_POST['password'];

$sql = "SELECT * FROM users WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password_input, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        header("Location: ../product.html");
        exit;
    } else {
        echo "<script>alert('Invalid email or password'); window.location.href='../login.html';</script>";
    }
} else {
    echo "<script>alert('Invalid email or password'); window.location.href='../login.html';</script>";
}

$conn->close();
?>
