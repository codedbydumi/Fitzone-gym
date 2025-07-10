<?php
// Database configuration
$host = 'localhost';
$dbname = 'nalifitzone';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch users from database
function getUsers($pdo) {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY date DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch classes from database
function getClasses($pdo) {
    $stmt = $pdo->query("SELECT * FROM classes ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get statistics
function getStats($pdo) {
    $stats = [];
    
    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $stats['totalUsers'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Active trainers (assuming usertype = 'trainer' or similar)
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE usertype = 'trainer' OR usertype = 'Trainer'");
    $stats['activeTrainers'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total classes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM classes");
    $stats['totalClasses'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Calculate monthly revenue (sum of class prices)
    $stmt = $pdo->query("SELECT SUM(price) as revenue FROM classes WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
    $revenue = $stmt->fetch(PDO::FETCH_ASSOC)['revenue'];
    $stats['monthlyRevenue'] = $revenue ? '$' . number_format($revenue, 2) : '$0.00';
    
    return $stats;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_user':
                $stmt = $pdo->prepare("INSERT INTO users (name, email, username, usertype, password, gender, date) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$_POST['name'], $_POST['email'], $_POST['username'], $_POST['usertype'], $_POST['password'], $_POST['gender']]);
                break;
                
            case 'edit_user':
                $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, username=?, usertype=?, gender=? WHERE id=?");
                $stmt->execute([$_POST['name'], $_POST['email'], $_POST['username'], $_POST['usertype'], $_POST['gender'], $_POST['id']]);
                break;
                
            case 'delete_user':
                $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
                $stmt->execute([$_POST['id']]);
                break;
                
            case 'add_class':
                $stmt = $pdo->prepare("INSERT INTO classes (class_name, trainer, schedule_days, schedule_time, price, duration, capacity, description, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$_POST['class_name'], $_POST['trainer'], $_POST['schedule_days'], $_POST['schedule_time'], $_POST['price'], $_POST['duration'], $_POST['capacity'], $_POST['description']]);
                break;
                
            case 'edit_class':
                $stmt = $pdo->prepare("UPDATE classes SET class_name=?, trainer=?, schedule_days=?, schedule_time=?, price=?, duration=?, capacity=?, description=? WHERE id=?");
                $stmt->execute([$_POST['class_name'], $_POST['trainer'], $_POST['schedule_days'], $_POST['schedule_time'], $_POST['price'], $_POST['duration'], $_POST['capacity'], $_POST['description'], $_POST['id']]);
                break;
                
            case 'delete_class':
                $stmt = $pdo->prepare("DELETE FROM classes WHERE id=?");
                $stmt->execute([$_POST['id']]);
                break;
        }
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

$users = getUsers($pdo);
$classes = getClasses($pdo);
$stats = getStats($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Fitness Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="css/oe.css">
</head>
<body>
  <header>
    <div class="logo">FITNESS<span>ADMIN</span></div>
    <nav>
      <ul>
        <li>
          <a href="#" onclick="showNotifications()">
            <i class="fas fa-bell"></i>
            <span class="notification-badge">3</span>
          </a>
        </li>
        <li><a href="#" onclick="showSettings()"><i class="fas fa-cog"></i></a></li>
        <li class="user-profile" onclick="showUserMenu()">
          <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Admin">
          <span>Admin User</span>
        </li>
      </ul>
    </nav>
  </header>

  <div class="dashboard-container">
    <aside class="sidebar">
      <ul class="sidebar-menu">
        <li><a href="#" class="active" onclick="showDashboard()"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="#" onclick="showUserManagement()"><i class="fas fa-users"></i> User Management</a></li>
        <li><a href="#" onclick="showTrainerManagement()"><i class="fas fa-user-tie"></i> Trainer Management</a></li>
        <li><a href="#" onclick="showClassManagement()"><i class="fas fa-calendar-alt"></i> Class Management</a></li>
        <li><a href="#" onclick="showNotifications()"><i class="fas fa-bullhorn"></i> Notifications</a></li>
        <li><a href="#" onclick="showReports()"><i class="fas fa-chart-bar"></i> Reports & Analytics</a></li>
        <li><a href="#" onclick="showFeedback()"><i class="fas fa-comments"></i> Queries & Feedback</a></li>
        <li><a href="#" onclick="showSettings()"><i class="fas fa-cog"></i> Settings</a></li>
        <li><a href="index.php"></i>Back </a></li>
      </ul>
    </aside>

    <main class="main-content">
      <div class="dashboard-header">
        <h1 class="dashboard-title">Admin Dashboard</h1>
        <div class="dark-mode-toggle" onclick="toggleDarkMode()">
          <i class="fas fa-moon"></i>
          <span>Dark Mode</span>
        </div>
      </div>

      <!-- Stats Overview -->
      <div class="stats-grid">
        <div class="stats-card">
          <div class="stats-number" id="totalUsers"><?php echo $stats['totalUsers']; ?></div>
          <div class="stats-label">Total Users</div>
        </div>
        <div class="stats-card">
          <div class="stats-number" id="activeTrainers"><?php echo $stats['activeTrainers']; ?></div>
          <div class="stats-label">Active Trainers</div>
        </div>
        <div class="stats-card">
          <div class="stats-number" id="totalClasses"><?php echo $stats['totalClasses']; ?></div>
          <div class="stats-label">Total Classes</div>
        </div>
        <div class="stats-card">
          <div class="stats-number" id="monthlyRevenue"><?php echo $stats['monthlyRevenue']; ?></div>
          <div class="stats-label">Monthly Revenue</div>
        </div>
      </div>

      <div class="card-grid">
        <!-- User Management Card -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">User Management</h3>
            <i class="fas fa-users card-icon"></i>
          </div>
          <p>Manage all users, roles, and permissions.</p>
          <div class="mt-3">
            <a href="#" class="btn btn-primary" onclick="showUserManagement()">
              <i class="fas fa-users"></i> View Users
            </a>
          </div>
        </div>

        <!-- Trainer Management Card -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Trainer Management</h3>
            <i class="fas fa-user-tie card-icon"></i>
          </div>
          <p>Add, edit, or remove trainers and assign classes.</p>
          <div class="mt-3">
            <a href="#" class="btn btn-primary" onclick="showTrainerManagement()">
              <i class="fas fa-user-tie"></i> Manage Trainers
            </a>
          </div>
        </div>

        <!-- Class Management Card -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Class Management</h3>
            <i class="fas fa-dumbbell card-icon"></i>
          </div>
          <p>Create and schedule fitness classes.</p>
          <div class="mt-3">
            <a href="#" class="btn btn-primary" onclick="showClassManagement()">
              <i class="fas fa-dumbbell"></i> Manage Classes
            </a>
          </div>
        </div>

        <!-- Reports Card -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Reports & Analytics</h3>
            <i class="fas fa-chart-line card-icon"></i>
          </div>
          <p>View platform statistics and insights.</p>
          <div class="mt-3">
            <a href="#" class="btn btn-primary" onclick="showReports()">
              <i class="fas fa-chart-line"></i> View Reports
            </a>
          </div>
        </div>
      </div>

      <!-- Recent Users Table -->
      <div class="card mt-3">
        <div class="card-header">
          <h3 class="card-title">Recent Registrations</h3>
          <i class="fas fa-user-plus card-icon"></i>
        </div>
        <div class="search-bar">
          <input type="text" placeholder="Search users..." onkeyup="searchUsers(this.value)">
          <button class="btn btn-primary" onclick="showAddUserModal()">
            <i class="fas fa-plus"></i> Add User
          </button>
        </div>
        <table class="table" id="usersTable">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Username</th>
              <th>Role</th>
              <th>Gender</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="usersTableBody">
            <?php foreach ($users as $index => $user): ?>
            <tr>
              <td><?php echo htmlspecialchars($user['name']); ?></td>
              <td><?php echo htmlspecialchars($user['email']); ?></td>
              <td><?php echo htmlspecialchars($user['username']); ?></td>
              <td><?php echo htmlspecialchars($user['usertype']); ?></td>
              <td><?php echo htmlspecialchars($user['gender']); ?></td>
              <td><?php echo htmlspecialchars($user['date']); ?></td>
              <td>
                <button class="btn btn-secondary" onclick="editUser(<?php echo $user['id']; ?>)">
                  <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-danger" onclick="deleteUser(<?php echo $user['id']; ?>)">
                  <i class="fas fa-trash"></i> Delete
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Upcoming Classes -->
      <div class="card mt-3">
        <div class="card-header">
          <h3 class="card-title">Fitness Classes</h3>
          <i class="fas fa-calendar-alt card-icon"></i>
        </div>
        <div class="search-bar">
          <input type="text" placeholder="Search classes..." onkeyup="searchClasses(this.value)">
          <button class="btn btn-primary" onclick="showAddClassModal()">
            <i class="fas fa-plus"></i> Add Class
          </button>
        </div>
        <table class="table" id="classesTable">
          <thead>
            <tr>
              <th>Class Name</th>
              <th>Trainer</th>
              <th>Schedule</th>
              <th>Time</th>
              <th>Price</th>
              <th>Duration</th>
              <th>Capacity</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="classesTableBody">
            <?php foreach ($classes as $index => $class): ?>
            <tr>
              <td><?php echo htmlspecialchars($class['class_name']); ?></td>
              <td><?php echo htmlspecialchars($class['trainer']); ?></td>
              <td><?php echo htmlspecialchars($class['schedule_days']); ?></td>
              <td><?php echo htmlspecialchars($class['schedule_time']); ?></td>
              <td>$<?php echo htmlspecialchars($class['price']); ?></td>
              <td><?php echo htmlspecialchars($class['duration']); ?> min</td>
              <td><?php echo htmlspecialchars($class['capacity']); ?></td>
              <td>
                <button class="btn btn-secondary" onclick="editClass(<?php echo $class['id']; ?>)">
                  <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-danger" onclick="deleteClass(<?php echo $class['id']; ?>)">
                  <i class="fas fa-trash"></i> Delete
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <!-- Add/Edit User Modal -->
  <div id="userModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="userModalTitle">Add New User</h2>
        <button class="close" onclick="closeModal('userModal')">&times;</button>
      </div>
      <form id="userForm" method="POST">
        <input type="hidden" name="action" id="userAction" value="add_user">
        <input type="hidden" name="id" id="userId">
        <div class="form-group">
          <label for="userName">Name:</label>
          <input type="text" id="userName" name="name" required>
        </div>
        <div class="form-group">
          <label for="userEmail">Email:</label>
          <input type="email" id="userEmail" name="email" required>
        </div>
        <div class="form-group">
          <label for="userUsername">Username:</label>
          <input type="text" id="userUsername" name="username" required>
        </div>
        <div class="form-group">
          <label for="userRole">Role:</label>
          <select id="userRole" name="usertype" required>
            <option value="">Select Role</option>
            <option value="Customer">Customer</option>
            <option value="Trainer">Trainer</option>
            <option value="Admin">Admin</option>
            <option value="Staff">Staff</option>
          </select>
        </div>
        <div class="form-group">
          <label for="userGender">Gender:</label>
          <select id="userGender" name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
          </select>
        </div>
        <div class="form-group" id="passwordGroup">
          <label for="userPassword">Password:</label>
          <input type="password" id="userPassword" name="password">
        </div>
        <div class="mt-3">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save User
          </button>
          <button type="button" class="btn btn-secondary" onclick="closeModal('userModal')">
            Cancel
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Add/Edit Class Modal -->
  <div id="classModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="classModalTitle">Add New Class</h2>
        <button class="close" onclick="closeModal('classModal')">&times;</button>
      </div>
      <form id="classForm" method="POST">
        <input type="hidden" name="action" id="classAction" value="add_class">
        <input type="hidden" name="id" id="classId">
        <div class="form-group">
          <label for="className">Class Name:</label>
          <input type="text" id="className" name="class_name" required>
        </div>
        <div class="form-group">
          <label for="classTrainer">Trainer:</label>
          <input type="text" id="classTrainer" name="trainer" required>
        </div>
        <div class="form-group">
          <label for="classSchedule">Schedule Days:</label>
          <input type="text" id="classSchedule" name="schedule_days" placeholder="e.g., Monday,Wednesday" required>
        </div>
        <div class="form-group">
          <label for="classTime">Schedule Time:</label>
          <input type="text" id="classTime" name="schedule_time" placeholder="e.g., 6:00 PM - 7:00 PM" required>
        </div>
        <div class="form-group">
          <label for="classPrice">Price:</label>
          <input type="number" id="classPrice" name="price" step="0.01" min="0" required>
        </div>
        <div class="form-group">
          <label for="classDuration">Duration (minutes):</label>
          <input type="number" id="classDuration" name="duration" min="15" max="180" required>
        </div>
        <div class="form-group">
          <label for="classCapacity">Capacity:</label>
          <input type="number" id="classCapacity" name="capacity" min="1" max="50" required>
        </div>
        <div class="form-group">
          <label for="classDescription">Description:</label>
          <textarea id="classDescription" name="description" rows="3"></textarea>
        </div>
        <div class="mt-3">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Class
          </button>
          <button type="button" class="btn btn-secondary" onclick="closeModal('classModal')">
            Cancel
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Convert PHP data to JavaScript
    let users = <?php echo json_encode($users); ?>;
    let classes = <?php echo json_encode($classes); ?>;

    // Dark mode toggle
    function toggleDarkMode() {
      const body = document.body;
      const currentTheme = body.getAttribute('data-theme');
      
      if (currentTheme === 'dark') {
        body.removeAttribute('data-theme');
      } else {
        body.setAttribute('data-theme', 'dark');
      }
    }

    // Navigation functions
    function showDashboard() {
      updateActiveMenuItem('Dashboard');
      document.querySelector('.dashboard-title').textContent = 'Admin Dashboard';
      location.reload();
    }

    function showUserManagement() {
      updateActiveMenuItem('User Management');
      document.querySelector('.dashboard-title').textContent = 'User Management';
      document.getElementById('usersTable').scrollIntoView({ behavior: 'smooth' });
    }

    function showTrainerManagement() {
      updateActiveMenuItem('Trainer Management');
      document.querySelector('.dashboard-title').textContent = 'Trainer Management';
      alert('Trainer Management section - Feature coming soon!');
    }

    function showClassManagement() {
      updateActiveMenuItem('Class Management');
      document.querySelector('.dashboard-title').textContent = 'Class Management';
      document.getElementById('classesTable').scrollIntoView({ behavior: 'smooth' });
    }

    function showNotifications() {
      updateActiveMenuItem('Notifications');
      document.querySelector('.dashboard-title').textContent = 'Notifications';
      alert('You have 3 new notifications:\n1. New user registration\n2. Class capacity reached\n3. Trainer availability update');
    }

    function showReports() {
      updateActiveMenuItem('Reports & Analytics');
      document.querySelector('.dashboard-title').textContent = 'Reports & Analytics';
      alert('Reports & Analytics section - Feature coming soon!');
    }

    function showFeedback() {
      updateActiveMenuItem('Queries & Feedback');
      document.querySelector('.dashboard-title').textContent = 'Queries & Feedback';
      alert('Queries & Feedback section - Feature coming soon!');
    }

    function showSettings() {
      updateActiveMenuItem('Settings');
      document.querySelector('.dashboard-title').textContent = 'Settings';
      alert('Settings section - Feature coming soon!');
    }

    function logout() {
      if (confirm('Are you sure you want to logout?')) {
        alert('Logging out...');
        window.location.href = '/login';
      }
    }

    function updateActiveMenuItem(activeItem) {
      document.querySelectorAll('.sidebar-menu a').forEach(link => {
        link.classList.remove('active');
        if (link.textContent.trim().includes(activeItem)) {
          link.classList.add('active');
        }
      });
    }

    // User management functions
    function showAddUserModal() {
      document.getElementById('userModalTitle').textContent = 'Add New User';
      document.getElementById('userForm').reset();
      document.getElementById('userAction').value = 'add_user';
      document.getElementById('userId').value = '';
      document.getElementById('passwordGroup').style.display = 'block';
      document.getElementById('userPassword').required = true;
      showModal('userModal');
    }

    function editUser(id) {
      const user = users.find(u => u.id == id);
      if (user) {
        document.getElementById('userModalTitle').textContent = 'Edit User';
        document.getElementById('userAction').value = 'edit_user';
        document.getElementById('userId').value = user.id;
        document.getElementById('userName').value = user.name;
        document.getElementById('userEmail').value = user.email;
        document.getElementById('userUsername').value = user.username;
        document.getElementById('userRole').value = user.usertype;
        document.getElementById('userGender').value = user.gender;
        document.getElementById('passwordGroup').style.display = 'none';
        document.getElementById('userPassword').required = false;
        showModal('userModal');
      }
    }

    function deleteUser(id) {
      if (confirm('Are you sure you want to delete this user?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
          <input type="hidden" name="action" value="delete_user">
          <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
      }
    }

    function searchUsers(query) {
      const rows = document.querySelectorAll('#usersTableBody tr');
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(query.toLowerCase())) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    // Class management functions
    function showAddClassModal() {
      document.getElementById('classModalTitle').textContent = 'Add New Class';
      document.getElementById('classForm').reset();
      document.getElementById('classAction').value = 'add_class';
      document.getElementById('classId').value = '';
      showModal('classModal');
    }

    function editClass(id) {
      const classItem = classes.find(c => c.id == id);
      if (classItem) {
        document.getElementById('classModalTitle').textContent = 'Edit Class';
        document.getElementById('classAction').value = 'edit_class';
        document.getElementById('classId').value = classItem.id;
        document.getElementById('className').value = classItem.class_name;
        document.getElementById('classTrainer').value = classItem.trainer;
        document.getElementById('classSchedule').value = classItem.schedule_days;
        document.getElementById('classTime').value = classItem.schedule_time;
        document.getElementById('classPrice').value = classItem.price;
        document.getElementById('classDuration').value = classItem.duration;
        document.getElementById('classCapacity').value = classItem.capacity;
        document.getElementById('classDescription').value = classItem.description;
        showModal('classModal');
      }
    }

    function deleteClass(id) {
      if (confirm('Are you sure you want to delete this class?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
          <input type="hidden" name="action" value="delete_class">
          <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
      }
    }

    function searchClasses(query) {
      const rows = document.querySelectorAll('#classesTableBody tr');
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(query.toLowerCase())) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    // Modal functions
    function showModal(modalId) {
      document.getElementById(modalId).classList.add('active');
    }

    function closeModal(modalId) {
      document.getElementById(modalId).classList.remove('active');
    }

    function showUserMenu() {
      alert('User Menu:\n1. Profile Settings\n2. Account Settings\n3. Logout');
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
      if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
      }
    });
  </script>
</body>
</html>