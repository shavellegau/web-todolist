<?php
require 'config.php';
header('Content-Type: application/json');

$list = $_GET['list'];
$result = mysqli_query($conn, "SELECT * FROM tasks WHERE list_name = '" . mysqli_real_escape_string($conn, $list) . "'");
$tasks = [];

while ($row = mysqli_fetch_assoc($result)) {
    $tasks[] = $row; 
}

echo json_encode($tasks); 
?>
