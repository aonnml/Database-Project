<?php
$servername = "db";
$username = "admin";
$password = "admin1234";
$dbname = "shipphi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $_POST['name'];
$lastname = $_POST['lastname'];
//$address = $_POST['address'];
$email = $_POST['email'];
$password_plain = $_POST['password'];

$check_sql = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($check_sql);

if ($result->num_rows > 0) {
    echo "<script>alert('This email is already registered. Please use another email.'); window.history.back();</script>";
} else {
    $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, lastname, email, password)
            VALUES ('$name', '$lastname', '$email', '$password_hashed')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Register successful!'); window.location.href='../login.html';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
