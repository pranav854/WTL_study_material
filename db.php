<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "study_materials";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed.");
}

$conn->set_charset("utf8mb4");
?>