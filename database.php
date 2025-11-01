<?php
$servername = "localhost";
$username = "root"; // default for XAMPP
$password = "";     // default empty
$dbname = "gym_management"; // your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>