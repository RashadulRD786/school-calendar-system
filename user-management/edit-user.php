<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // Your database password
$dbname = 'school_system';
$port = 3307; // Ensure port is included if it was previously 3307

$conn = new mysqli($host, $user, $pass, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST request to update user data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if required fields are set
    if (isset($_POST['id'], $_POST['email'], $_POST['name'], $_POST['role'])) {
        $id = intval($_POST['id']);
        $email = trim($_POST['email']);
        $name = trim($_POST['name']);
        $role = trim($_POST['role']);
        // Get the password input, use null coalescing to default to empty string if not set
        $password_input = trim($_POST['password'] ?? '');

        $update_password = false;
        $hashed_password = null;

        // Check if a new password was provided in the input field
        if (!empty($password_input)) {
            // Hash the new password
            $hashed_password = password_hash($password_input, PASSWORD_DEFAULT);
            if ($hashed_password === false) {
                // Handle error if hashing fails
                echo "Error: Failed to hash password.";
                $conn->close();
                exit();
            }
            $update_password = true; // Flag to indicate that password needs to be updated
        }

        // Prepare the SQL statement based on whether the password is being updated
        if ($update_password) {
            // Update all fields including password_hash
            // IMPORTANT: Update 'password_hash' column, not 'password'
            $stmt = $conn->prepare("UPDATE users SET email = ?, password_hash = ?, name = ?, role = ? WHERE id = ?");
            if (!$stmt) {
                echo "Error preparing statement (with password update): " . $conn->error;
                $conn->close();
                exit();
            }
            // Bind parameters: email, hashed_password, name, role, id
            $stmt->bind_param("ssssi", $email, $hashed_password, $name, $role, $id);
        } else {
            // Update fields excluding password_hash (if no new password was provided)
            $stmt = $conn->prepare("UPDATE users SET email = ?, name = ?, role = ? WHERE id = ?");
            if (!$stmt) {
                echo "Error preparing statement (without password update): " . $conn->error;
                $conn->close();
                exit();
            }
            // Bind parameters: email, name, role, id
            $stmt->bind_param("sssi", $email, $name, $role, $id);
        }

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect to user-management.php after successful update
            header("Location: user-management.php");
            exit();
        } else {
            echo "Error updating user: " . $stmt->error;
        }
        $stmt->close(); // Close the prepared statement
    } else {
        echo "Missing required fields.";
    }
}

// Handle GET request to show the edit form
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Select user details for the form. No need to select password_hash as it's not displayed.
    $stmt = $conn->prepare("SELECT email, name, role FROM users WHERE id = ?");
    if (!$stmt) {
        echo "Error preparing statement (GET): " . $conn->error;
        $conn->close();
        exit();
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($email, $name, $role);

    if ($stmt->fetch()) {
        // If user data is found, display the edit form
        ?>
        <form method="POST" action="edit-user.php">
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <label>Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required><br>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required><br>

            <label>Password:</label>
            <input type="password" name="password" placeholder="Leave blank to keep current password"><br>

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
    $stmt->close(); // Close the prepared statement
} else {
    echo "Invalid request.";
}

$conn->close(); // Close the database connection
?>