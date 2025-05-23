<?php
$servername = "localhost"; // Usually "localhost" for local development
$username = "root";        // Your MySQL username
$password = "";            // Your MySQL password (often empty for root on XAMPP/WAMP)
$dbname = "gestion_parc_auto"; // The database you created

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Optional: Set character set to UTF-8
$conn->set_charset("utf8");
?>