<?php
header('Content-Type: application/json');
include 'db.php';

$sql = "SELECT chat.*, users.username FROM chat 
        JOIN users ON chat.user_id = users.id 
        ORDER BY chat.id DESC LIMIT 30";
$result = $conn->query($sql);

$message = [];
while($row = $result->fetch_assoc()) {
    $message[] = $row;
}
echo json_encode(array_reverse($message));

?>
