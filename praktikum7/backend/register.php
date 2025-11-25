<?php
header('Content-Type: application/json');
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

$username = $conn->real_escape_string($data['username']);
$password_raw = $data['password'];
$password_hashed = password_hash($password_raw, PASSWORD_BCRYPT);

if ($username === '' || $password_raw === '') {
    echo json_encode(['status' => 'error', 'message' => 'Username/Password harus diisi!']);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Username sudah digunakan']);
    $stmt->close();
    $conn->close();
    exit;
} else { 
    $insert_stmt = $conn->prepare("INSERT INTO users (username, password, status) VALUES (?, ?, 'offline')");
    $insert_stmt->bind_param("ss", $username, $password_hashed);

    if($insert_stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Registrasi berhasil!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal registrasi']);
    }
}
$stmt->close();

$insert_stmt->close();
$conn->close();

?>
