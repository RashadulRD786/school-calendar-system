<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'school_system';

$conn = new mysqli($host, $user, $pass, $dbname, 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'], $_POST['email'], $_POST['password'], $_POST['name'], $_POST['role'])) {
        $id = intval($_POST['id']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $name = trim($_POST['name']);
        $role = trim($_POST['role']);

        $stmt = $conn->prepare("UPDATE users SET email = ?, password = ?, name = ?, role = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $email, $password, $name, $role, $id);

        if ($stmt->execute()) {
            header("Location: user-management.php");
            exit();
        } else {
            echo "Error updating user: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Missing required fields.";
    }
}

// Handle GET (Show Form)
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT email, name, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($email, $name, $role);

    if ($stmt->fetch()) {
        // Show the form only if data exists
        ?>
        <form method="POST" action="edit-user.php">
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <label>Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required><br>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required><br>

            <label>Password:</label>
            <input type="password" name="password" required><br>

            <label>Role:</label>
            <select name="role" required>
                <option value="Admin" <?php echo ($role === 'Admin') ? 'selected' : ''; ?>>Admin</option>
                <option value="Teacher" <?php echo ($role === 'Teacher') ? 'selected' : ''; ?>>Teacher</option>
            </select><br><br>

            <button type="submit">Update</button>
        </form>
        
        <?php
    } else {
        echo "User not found.";
    }
    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
