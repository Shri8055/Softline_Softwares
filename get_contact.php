<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ss";

$conn = new mysqli($servername, $username, $password, $dbname, 4306);

$data = [];

$sql = "SELECT * FROM contact_queries";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($data);
