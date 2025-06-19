<?php
$host = "localhost";        // Change if hosted remotely
$username = "root";         // Your MySQL username
$password = "";             // Your MySQL password
$database = "school_system";    // Your database name

// Create connection
$conn = mysqli_connect($host, $username, $password, $database,3307);

// Check connection
if (!$conn) {
    die("❌ Connection failed: " . mysqli_connect_error());
}

// Optional: Set character encoding
mysqli_set_charset($conn, "utf8");

// ✅ Connection successful
?>
