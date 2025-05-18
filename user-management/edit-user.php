<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // default for XAMPP
$dbname = 'school-system';

$conn = new mysqli($host, $user, $pass, $dbname,3307);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'], $_POST['email'], $_POST['password'])) {
        $id = intval($_POST['id']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // UPDATE user
        $stmt = $conn->prepare("UPDATE user_management SET email = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssi", $email, $password, $id);

        if ($stmt->execute()) {
            // Redirect to user list after success
            header("Location: user-management.php");
            exit();
        } else {
            echo "Error updating user: " . $stmt->error;
        }
    } else {
        echo "Missing required fields.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    // Load user data for form
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT email FROM user_management WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();
    ?>

    <!-- Basic Edit Form -->
    <form method="POST" action="edit-user.php">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Update</button>
    </form>

    <?php
} else {
    echo "Invalid request.";
}

$conn->close();
?>
