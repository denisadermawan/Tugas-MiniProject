<?php
header('Content-Type: application/json');
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $data['user_id'];
$message = $conn->real_escape_string($data['message']);

$sql = "INSERT INTO chat (user_id, message, created_at) VALUES ($user_id, '$message', NOW())";
$conn->query($sql);

echo json_encode(["status" => "success"]);

?>
