<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "db_jualbaju";

$conn = new mysqli($host, $user, $pass, $db);

if (!$conn) {
 die("Koneksi database gagal: " . $conn->connect_error);
}
?>