<?php
// connect.php
$host = "localhost"; // Database host
$username = "root"; // Database username
$password = ""; // Database password
$database = "attmgsystem"; // Database name

// Create connection
$mysqli = new mysqli($host, $username, $password, $database);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>