<?php
require "connect.php";
session_start();

$data = json_decode(file_get_contents("php://input"), true);
$cart_id = $data['cart_id'];

$sql = "DELETE FROM cart WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cart_id);

$conn->begin_transaction();

try {
	if (!$stmt->execute()) {
		throw new Exception('execute failed');
	}

	$conn->commit();
	echo json_encode(["status" => "success"]);
} catch (Throwable $e) {
	$conn->rollback();
	echo json_encode([
		"status" => "error",
		"message" => "Unable to remove item"
	]);
}

$stmt->close();
$conn->close();
