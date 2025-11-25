<?php
session_start();
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
if(!$data || !isset($data['user_id'], $data['current_username'], $data['new_username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit;
}

$user_id = $conn->real_escape_string($data['user_id']);
$current_username = $conn->real_escape_string($data['current_username']);
$new_username = $conn->real_escape_string($data['new_username']);


$stmt = $conn->prepare("SELECT username FROM users WHERE id = ? AND username = ?");
$stmt->bind_param("is", $user_id, $current_username);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Username saat ini salah atau user tidak ditemukan']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

$update_stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
$update_stmt->bind_param("si", $new_username, $user_id);
if($update_stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Username berhasil diubah!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah username']);
}

$update_stmt->close();
$conn->close();
?>
