<?php
// Enable error reporting (for debugging - disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database credentials
$servername = "localhost";
$username = "root"; // Update if your phpMyAdmin username is different
$password = "";     // Update with your actual password
$database = "school_system"; // Your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database,3307);

// Check for DB connection errors
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get dates from POST
$startDate = $_POST['startDate'] ?? null;
$endDate = $_POST['endDate'] ?? null;

// Validate date inputs
if (!$startDate || !$endDate) {
    die("Error: Start date and end date are required.");
}

// Prepare SQL query
$query = "SELECT * FROM events WHERE date BETWEEN ? AND ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

// Set headers to output Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=event_report.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Output table header
echo "<table border='1'>";
echo "<tr>
        <th>ID</th>
        <th>Title</th>
        <th>Description</th>
        <th>Event Date</th>
      </tr>";

// Check if any data was returned
if ($result->num_rows === 0) {
    echo "<tr><td colspan='4'>No data found for selected date range.</td></tr>";
} else {
    // Output table rows
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['id']) . "</td>
                <td>" . htmlspecialchars($row['title']) . "</td>
                <td>" . htmlspecialchars($row['description']) . "</td>
                <td>" . htmlspecialchars($row['event_date']) . "</td>
              </tr>";
    }
}
echo "</table>";

// Cleanup
$stmt->close();
$conn->close();
?>