<?php
include 'config.php';

$id = $_POST['id'];
$rate = $_POST['rate'];
$desc = $_POST['description'];

$sql = "UPDATE review SET rate=?, description=?, is_reviewed=1 WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("dsi", $rate, $desc, $id);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error";
}
?>
