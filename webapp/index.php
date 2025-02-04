<?php
$host = 'mariadb';
$user = 'root';
$pass = 'example';
$db = 'testdb';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully to MariaDB!";
?>
