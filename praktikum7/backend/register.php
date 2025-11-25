<?php
header('Content-Type: application/json');
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$username = $conn->real_escape_string($data['username']);
$password = password_hash($data['password'], PASSWORD_BCRYPT);

if ($username == '' || $password == '') {
    echo json_encode(["status" => "error", "message" => "Username / password kosong"]);
    exit;
} else {
    $sql = "INSERT INTO users (username, password, status) VALUES ('$username', '$password', 'offline')";
    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "Registrasi berhasil!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal registrasi"]);
    }
}


?>
