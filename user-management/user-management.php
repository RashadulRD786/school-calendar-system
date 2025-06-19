<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - User Management</title>
  <link href="https://fonts.googleapis.com/css2?family=Wix+Madefor+Display&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="user-management.css" />
  <style>
  .modal-overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100vw; height: 100vh;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.modal-card {
  background: #fff;
  padding: 20px 30px;
  border-radius: 10px;
  position: relative;
  width: 320px;
  text-align: center;
}

.modal-close {
  position: absolute;
  top: 10px;
  right: 15px;
  font-size: 20px;
  border: none;
  background: transparent;
  cursor: pointer;
}

.modal-actions {
  margin-top: 20px;
  display: flex;
  justify-content: center;
  gap: 10px;
}

.btn-confirm {
  background-color: #28a745;
  color: white;
  padding: 10px 16px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.btn-cancel {
  background-color: #dc3545;
  color: white;
  padding: 10px 16px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.hidden {
  display: none;
}

</style>
</head>
<body>
  <div class="dashboard-container">
    <header class="dashboard-header">
      <div class="header-left">
        <div class="logo-container">
          <img src="../SMKLOGO.png" alt="EventFlow Logo" class="logo-image" />
        </div>
        <div class="header-text">
          <h1 class="company-name">SK SAUJANA UTAMA</h1>
          <p class="company-tagline">Event Management System</p>
        </div>
      </div>
      <div class="welcome-message">Welcome  Admin 👋</div>
      <div class="notification-container">
          <button class="notification-button" data-el="button-1">
            <svg viewBox="0 0 53 58" fill="none" class="notification-icon">
              <rect width="53" height="57" fill="none"></rect>
              <path
                d="M5.58695 34.1858C5.11735 37.3994 7.21688 39.6297 9.78749 40.7412C19.6427 45.0029 33.3574 45.0029 43.2125 40.7412C45.7832 39.6297 47.8827 37.3994 47.4132 34.1858C47.1246 32.2109 45.6975 30.5665 44.6402 28.9608C43.2553 26.8316 43.1178 24.5093 43.1175 22.0386C43.1175 12.4904 35.6777 4.75 26.5 4.75C17.3225 4.75 9.88262 12.4904 9.88262 22.0386C9.8824 24.5093 9.7448 26.8316 8.35993 28.9608C7.30265 30.5665 5.87556 32.2109 5.58695 34.1858Z"
                stroke="#FFFFFF"
                stroke-width="3"
                stroke-linecap="round"
                stroke-linejoin="round"
              ></path>
              <path
                d="M19.875 53.4375C21.6331 54.9145 23.9549 55.8125 26.5 55.8125C29.0451 55.8125 31.3669 54.9145 33.125 53.4375"
                stroke="#FFFFFF"
                stroke-width="3"
                stroke-linecap="round"
                stroke-linejoin="round"
              ></path>
            </svg>
            <div class="notification-dropdown" data-el="div-1" hidden>
              <h3 class="notification-title">Notifications</h3>
              <p class="notification-message">No new notifications</p>
            </div>
          </button>
        </div>
    </header>

    <div class="dashboard-content">
      <nav class="sidebar">
        <div class="nav-links">
          <a href="../admin-dashboard1.html" class="nav-button dashboard-btn">Dashboard</a>
          
          <a href="../report.html" class="nav-button reports-btn">Reports</a>
          <a href="user-management.php" class="nav-button users-btn active">Admin</a>
        </div>
        <a href="../index.html" class="logout-button">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none">
            <path d="M16 17L21 12L16 7" stroke="#ec4547" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M21 12H9" stroke="#ec4547" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M12 19H5C3.89543 19 3 18.1046 3 17V7C3 5.89543 3.89543 5 5 5H12" stroke="#ec4547" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
          <span>Logout</span>
</a>
        
      </nav>

      <main class="main-content">
        <h2 class="page-title">All Users</h2><br><br>
        <div class="content-header1">
          <button id="addUserBtn" class="add-user-button" onclick="window.location.href='add-user1.html'">
            <span class="plus-icon">+</span>
            <span>Add User</span>
          </button>          
        </div>

        


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
          $result = $conn->query("SELECT * FROM users");
          while ($row = $result->fetch_assoc()):
          ?>
            <div class="table-row">
              <div class="cell email"><?= htmlspecialchars($row['email']) ?></div>
              <div class="cell actions">
                <button class="edit-button" onclick="editUser(<?= $row['id'] ?>, '<?= htmlspecialchars($row['email']) ?>', '<?= htmlspecialchars($row['role']) ?>','<?= htmlspecialchars($row['name']) ?>')">Edit</button>
                <button class="delete-button" onclick="confirmDelete(<?= $row['id'] ?>)">Delete</button>

              </div>
            </div>
          <?php endwhile; $conn->close(); ?>
        </div>
      </main>
    </div>
  </div>

  <script>
    const state = {
 notifications: false,

};

function onButton1Click(event) {
 
  state.notifications = !state.notifications;
  
  updateNotificationVisibility(); 
}


function onDiv1Click(event) {
  event.stopPropagation(); 
  state.notifications = false; 
  updateNotificationVisibility(); 
}


function setupNotificationListeners() {
  
  document.querySelectorAll("[data-el='button-1']").forEach((el) => {
    
    el.removeEventListener("click", onButton1Click);
    el.addEventListener("click", onButton1Click);
  });

 
  document.querySelectorAll("[data-el='div-1']").forEach((el) => {
    el.removeEventListener("click", onDiv1Click);
    el.addEventListener("click", onDiv1Click);
  });
}


function updateNotificationVisibility() {
  document.querySelectorAll("[data-el='div-1']").forEach((el) => {
    el.hidden = !state.notifications; 
  });
}


document.addEventListener("click", function (event) {
  const notificationButton = document.querySelector("[data-el='button-1']");
  const notificationDropdown = document.querySelector("[data-el='div-1']");

  
  if (state.notifications && notificationButton && notificationDropdown &&
      !notificationButton.contains(event.target) && !notificationDropdown.contains(event.target)) {
    state.notifications = false;
    updateNotificationVisibility();
  }
});


document.addEventListener("DOMContentLoaded", () => {


  setupNotificationListeners();
  updateNotificationVisibility(); 
});


    // Function to handle Edit
   function editUser(userId, userEmail, userRole,userName) {
      // Redirect to edit-user.html with query parameters
      window.location.href = `edit-user.html?id=${userId}&email=${encodeURIComponent(userEmail)}&name=${encodeURIComponent(userName)}&role=${encodeURIComponent(userRole)}`;
    }

  // Show modal and set user ID
let selectedUserId = null;

function confirmDelete(userId) {
  selectedUserId = userId;
  document.getElementById('deleteUserId').value = userId;
  document.getElementById('deleteConfirmationModal').classList.remove('hidden');
}



// Cancel or close modal
window.addEventListener('DOMContentLoaded', () => {
    const cancelBtn = document.getElementById('cancelDeleteBtn');
    const closeBtn = document.getElementById('closeDeleteModal');

    if (cancelBtn) {
      cancelBtn.addEventListener('click', closeModal);
    }

    if (closeBtn) {
      closeBtn.addEventListener('click', closeModal);
    }
  });
  
function closeModal() {
  document.getElementById('deleteConfirmationModal').classList.add('hidden');
  selectedUserId = null;
}

</script>

 
<!-- Delete Confirmation Modal -->
<div id="deleteConfirmationModal" class="modal-overlay hidden">
  <div class="modal-card">
    <button class="modal-close" id="closeDeleteModal">&times;</button>
    <p class="modal-title">Are you sure you want to delete this user?</p>
    <div class="modal-actions">
      <form method="POST" action="delete-user.php" id="deleteForm">
        <input type="hidden" name="id" id="deleteUserId" />
        <button type="submit" class="btn-confirm">Confirm</button>
        <button type="button" class="btn-cancel" id="cancelDeleteBtn">Cancel</button>
      </form>
    </div>
  </div>
</div>



</body>
</html>