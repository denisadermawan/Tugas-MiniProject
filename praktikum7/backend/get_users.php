<?php
header('Content-Type: application/json');
include 'db.php';

$sql = "SELECT username, status FROM users";
$result = $conn->query($sql);

$users = [];
while($row = $result->fetch_assoc()) {
    $users[] = $row;
}
echo json_encode($users);

?>
