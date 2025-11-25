<?php
$host = "localhost";
$user = "root";
$pass = "root";
$db = "vue_chat";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["error" => "Koneksi gagal: " . $conn->connect_error]));
}
?>

