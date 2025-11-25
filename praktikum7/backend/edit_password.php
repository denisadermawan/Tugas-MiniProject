<?php
session_start();
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
if(!$data || !isset($data['user_id'], $data['current_password'], $data['new_password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit;
}

$user_id = $conn->real_escape_string($data['user_id']);
$current_password = $data['current_password'];
$new_password = $data['new_password'];


$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'User tidak ditemukan']);
    $stmt->close();
    $conn->close();
    exit;
}

$row = $result->fetch_assoc();
$hashed_password = $row['password'];
$stmt->close();


if(!password_verify($current_password, $hashed_password)) {
    echo json_encode(['status' => 'error', 'message' => 'Password saat ini salah']);
    $conn->close();
    exit;
}

$hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

$update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$update_stmt->bind_param("si", $hashed_new_password, $user_id);
if($update_stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Password berhasil diubah!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah password']);
}

$update_stmt->close();
$conn->close();
?>
