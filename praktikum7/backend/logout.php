<?php
header('Content-Type: application/json');
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $conn->real_escape_string($data['user_id']);

$sql = "UPDATE users SET status='offline' WHERE id='$user_id'";
if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal update status"]);
}
?>
