<?php
require "connect.php";
session_start();

$data = json_decode(file_get_contents("php://input"), true);
$cart_id = $data['cart_id'];

$sql = "DELETE FROM cart WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cart_id);
$stmt->execute();

echo json_encode(["status" => "success"]);
