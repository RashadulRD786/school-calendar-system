<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - User Management</title>
  <link href="https://fonts.googleapis.com/css2?family=Wix+Madefor+Display&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="user-management.css" />
</head>
<body>
  <div class="dashboard-container">
    <header class="dashboard-header">
      <div class="header-left">
        <div class="logo-container">
          <img src="https://i.pinimg.com/564x/31/50/bf/3150bf915dad0ce70b152fae9f13cd0f.jpg" alt="EventFlow Logo" class="logo-image" />
        </div>
        <div class="header-text">
          <h1 class="company-name">SK SAUJANA UTAMA</h1>
          <p class="company-tagline">Event Management System</p>
        </div>
      </div>
      <div class="welcome-message">Welcome Admin 👋</div>
      <div class="notification-container">
        <button class="notification-button">
          <img src="https://c.animaapp.com/d3SSWScQ/img/notification-03.svg" alt="Notification Icon" class="notification-icon" />
        </button>
      </div>
    </header>

    <div class="dashboard-content">
      <nav class="sidebar">
        <div class="nav-links">
          <a href="../admin-dashboard1.html" class="nav-button dashboard-btn">Dashboard</a>
          <button class="nav-button events-btn">Events</button>
          <button class="nav-button reports-btn">Reports</button>
          <a href="user-management.php" class="nav-button users-btn active">Users</a>
        </div>
        <button class="logout-button">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none">
            <path d="M16 17L21 12L16 7" stroke="#ec4547" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M21 12H9" stroke="#ec4547" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M12 19H5C3.89543 19 3 18.1046 3 17V7C3 5.89543 3.89543 5 5 5H12" stroke="#ec4547" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
          <span>Logout</span>
        </button>
      </nav>

      <main class="main-content">
        <div class="content-header1">
          <button id="addUserBtn" class="add-user-button" onclick="window.location.href='add-user1.html'">
            <span class="plus-icon">+</span>
            <span>Add User</span>
          </button>          
        </div>

        <h2 class="page-title">All Users</h2>

        <div class="users-table-wrapper">
          <div class="table-header">
            <div class="header-cell email">Email</div>
            <div class="header-cell actions">Actions</div>
          </div>

          <?php
          $host = 'localhost';
          $username = 'root';
          $password = '';
          $database = 'school_system';
          $conn = new mysqli($host, $username, $password, $database,3307);
          if ($conn->connect_error) {
              die("Connection failed: " . $conn->connect_error);
          }
          $result = $conn->query("SELECT * FROM user_management");
          while ($row = $result->fetch_assoc()):
          ?>
            <div class="table-row">
              <div class="cell email"><?= htmlspecialchars($row['email']) ?></div>
              <div class="cell actions">
                <button class="edit-button" onclick="editUser(<?= $row['id'] ?>, '<?= htmlspecialchars($row['email']) ?>', '<?= htmlspecialchars($row['role']) ?>')">Edit</button>
                <button class="delete-button" onclick="confirmDelete(<?= $row['id'] ?>)">Delete</button>
              </div>
            </div>
          <?php endwhile; $conn->close(); ?>
        </div>
      </main>
    </div>
  </div>

  <script>
    // Function to handle Edit
    function editUser(userId, userEmail, userRole) {
      // Redirect to edit-user.html with query parameters
      window.location.href = `edit-user.html?id=${userId}&email=${encodeURIComponent(userEmail)}&role=${encodeURIComponent(userRole)}`;
    }

    // Function to handle Delete
    function confirmDelete(userId) {
      if (confirm("Are you sure you want to delete this user?")) {
        // Proceed with deleting the user
        window.location.href = `delete-user.php?id=${userId}`; // Redirect to delete-user.php with the user's ID
      }
    }
  </script>

  <!-- Delete Confirmation Modal -->
  <div id="deleteConfirmationModal" class="modal-overlay hidden">
    <div class="modal-card">
      <p class="modal-title">Are you sure you want to delete?</p>
      <div class="modal-actions">
        <button class="btn btn-confirm" id="confirmDeleteBtn">Confirm</button>
        <button class="btn btn-cancel" id="cancelDeleteBtn">Cancel</button>
      </div>
    </div>
  </div>
</body>
</html>
