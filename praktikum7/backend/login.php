<?php
header('Content-Type: application/json');
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$username = $conn->real_escape_string($data['username']);
$password = $data['password'];

$sql = "SELECT * FROM users WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $conn->query("UPDATE users SET status='online' WHERE id=".$user['id']);
        echo json_encode(["status" => "success", "user" => ["id"=>$user['id'], "username"=>$user['username']]]);
    } else {
        echo json_encode(["status" => "error", "message" => "Password salah"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "User tidak ditemukan"]);
}


?>
