<?php
$servername = "db";
$username = "admin";
$password = "admin1234";
$dbname = "shipphi";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
