<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "todo_list";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === FALSE) {
    echo "Error creating database: " . $conn->error;
}

// Select database
$conn->select_db($dbname);

// Create users table if not exists
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === FALSE) {
    echo "Error creating users table: " . $conn->error;
}

// Create tasks table if not exists
$sql = "CREATE TABLE IF NOT EXISTS tasks (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED NOT NULL,
    judul VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    status VARCHAR(30) NOT NULL DEFAULT 'Belum Selesai',
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === FALSE) {
    echo "Error creating tasks table: " . $conn->error;
}

// Handle Authentication
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // User Registration
    if (isset($_POST['register'])) {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encrypt password
        
        // Check if username or email already exists
        $check_query = "SELECT * FROM users WHERE username='$username' OR email='$email'";
        $result = $conn->query($check_query);
        
        if ($result->num_rows > 0) {
            $_SESSION['message'] = "Username atau email sudah digunakan!";
            $_SESSION['message_type'] = "error";
        } else {
            $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
            
            if ($conn->query($sql) === TRUE) {
                $_SESSION['message'] = "Registrasi berhasil! Silakan login.";
                $_SESSION['message_type'] = "success";
                header("Location: ?page=login");
                exit();
            } else {
                $_SESSION['message'] = "Error: " . $sql . "<br>" . $conn->error;
                $_SESSION['message_type'] = "error";
            }
        }
    }
    
    // User Login
    if (isset($_POST['login'])) {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = $_POST['password'];
        
        $sql = "SELECT * FROM users WHERE username='$username'";
        $result = $conn->query($sql);
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: ?page=dashboard");
                exit();
            } else {
                $_SESSION['message'] = "Password salah!";
                $_SESSION['message_type'] = "error";
            }
        } else {
            $_SESSION['message'] = "Username tidak ditemukan!";
            $_SESSION['message_type'] = "error";
        }
    }
    
    // Handle CRUD operations for Tasks
    if (isset($_SESSION['user_id'])) {
        // Add new task
        if (isset($_POST['add'])) {
            $user_id = $_SESSION['user_id'];
            $judul = mysqli_real_escape_string($conn, $_POST['judul']);
            $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
            $tanggal_mulai = $_POST['tanggal_mulai'];
            $tanggal_selesai = $_POST['tanggal_selesai'];
            
            $sql = "INSERT INTO tasks (user_id, judul, deskripsi, status, tanggal_mulai, tanggal_selesai)
                    VALUES ('$user_id', '$judul', '$deskripsi', 'Belum Selesai', '$tanggal_mulai', '$tanggal_selesai')";
            
            if ($conn->query($sql) === FALSE) {
                echo "Error: " . $sql . "<br>" . $conn->error;
            } else {
                header("Location: ?page=dashboard");
                exit();
            }
        }
        
        // Update task
        if (isset($_POST['update'])) {
            $user_id = $_SESSION['user_id'];
            $id = $_POST['id'];
            $judul = mysqli_real_escape_string($conn, $_POST['judul']);
            $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
            $status = $_POST['status'];
            $tanggal_mulai = $_POST['tanggal_mulai'];
            $tanggal_selesai = $_POST['tanggal_selesai'];
            
            $sql = "UPDATE tasks SET 
                    judul='$judul', 
                    deskripsi='$deskripsi', 
                    status='$status', 
                    tanggal_mulai='$tanggal_mulai', 
                    tanggal_selesai='$tanggal_selesai' 
                    WHERE id=$id AND user_id=$user_id";
            
            if ($conn->query($sql) === FALSE) {
                echo "Error updating record: " . $conn->error;
            } else {
                header("Location: ?page=dashboard");
                exit();
            }
        }
        
        // Change status
        if (isset($_POST['change_status'])) {
            $user_id = $_SESSION['user_id'];
            $id = $_POST['id'];
            $new_status = ($_POST['current_status'] == 'Selesai') ? 'Belum Selesai' : 'Selesai';
            
            $sql = "UPDATE tasks SET status='$new_status' WHERE id=$id AND user_id=$user_id";
            
            if ($conn->query($sql) === FALSE) {
                echo "Error updating status: " . $conn->error;
            } else {
                header("Location: ?page=dashboard");
                exit();
            }
        }
        
        // Delete task
        if (isset($_POST['delete'])) {
            $user_id = $_SESSION['user_id'];
            $id = $_POST['id'];
            
            $sql = "DELETE FROM tasks WHERE id=$id AND user_id=$user_id";
            
            if ($conn->query($sql) === FALSE) {
                echo "Error deleting record: " . $conn->error;
            } else {
                header("Location: ?page=dashboard");
                exit();
            }
        }
    }
}

// Logout action
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_unset();
    session_destroy();
    header("Location: ?page=login");
    exit();
}

// Routing system
$page = isset($_GET['page']) ? $_GET['page'] : (isset($_SESSION['user_id']) ? 'dashboard' : 'login');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Mari To List</title>
</head>
<body>
    <div class="header">
        <div class="logo">
            <div class="logo-circle">
                <span class="logo-text">To List</span>
            </div>
        </div>
        <?php if ($page == 'dashboard'): ?>
        <div class="search-bar">
            <input type="text" placeholder="Search...">
        </div>
        <?php endif; ?>
        <div class="title">Mari To list</div>
        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="user-info">
            <span>Hello, <?php echo $_SESSION['username']; ?></span>
            <a href="?action=logout">Logout</a>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="container">
        <?php if (isset($_SESSION['message'])): ?>
        <div class="message <?php echo $_SESSION['message_type']; ?>">
            <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            ?>
        </div>
        <?php endif; ?>
        
        <?php
        // Page Router
        switch ($page) {
            case 'login':
                include 'pages/login.php';
                break;
            case 'register':
                include 'pages/register.php';
                break;
            case 'dashboard':
                if (!isset($_SESSION['user_id'])) {
                    header("Location: ?page=login");
                    exit();
                }
                include 'pages/dashboard.php';
                break;
            default:
                include 'pages/login.php';
        }
        ?>
    </div>
    
    <script src="script.js"></script>
</body>
</html>

<?php
// Create the pages directory if it doesn't exist
if (!file_exists('pages')) {
    mkdir('pages', 0777, true);
}

// Create login.php file
$loginContent = '
<div class="auth-container">
    <h2 class="auth-title">Login</h2>
    <form method="post" action="">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" name="login" class="form-submit">Login</button>
    </form>
    <div class="auth-links">
        <p>Belum punya akun? <a href="?page=register">Daftar sekarang</a></p>
    </div>
</div>
';

// Create register.php file
$registerContent = '
<div class="auth-container">
    <h2 class="auth-title">Register</h2>
    <form method="post" action="">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" name="register" class="form-submit">Register</button>
    </form>
    <div class="auth-links">
        <p>Sudah punya akun? <a href="?page=login">Login</a></p>
    </div>
</div>
';

// Create dashboard.php file
$dashboardContent = '
<button class="add-btn" id="openAddModal">Tambah Tugas</button>

<table>
    <thead>
        <tr>
            <th>NO</th>
            <th>Judul</th>
            <th>Deskripsi</th>
            <th>Status</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Selesai</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        // Get user tasks
        $user_id = $_SESSION["user_id"];
        $sql = "SELECT * FROM tasks WHERE user_id = $user_id ORDER BY id DESC";
        $result = $conn->query($sql);
        
        $counter = 1;
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) { 
        ?>
        <tr>
            <td><?php echo $counter++; ?></td>
            <td><?php echo $row["judul"]; ?></td>
            <td><?php echo $row["deskripsi"]; ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
                    <input type="hidden" name="current_status" value="<?php echo $row["status"]; ?>">
                    <button type="submit" name="change_status" class="<?php echo ($row["status"] == \'Selesai\') ? \'status-done\' : \'status-pending\'; ?>">
                        <?php echo $row["status"]; ?>
                    </button>
                </form>
            </td>
            <td><?php echo date(\'d-m-Y\', strtotime($row["tanggal_mulai"])); ?></td>
            <td><?php echo date(\'d-m-Y\', strtotime($row["tanggal_selesai"])); ?></td>
            <td>
                <button class="btn btn-edit" onclick="openEditModal(<?php echo $row[\'id\']; ?>, \'<?php echo $row[\'judul\']; ?>\', \'<?php echo $row[\'deskripsi\']; ?>\', \'<?php echo $row[\'status\']; ?>\', \'<?php echo $row[\'tanggal_mulai\']; ?>\', \'<?php echo $row[\'tanggal_selesai\']; ?>\')">Edit</button>
                
                <button class="btn btn-delete" onclick="openDeleteModal(<?php echo $row[\'id\']; ?>, \'<?php echo $row[\'judul\']; ?>\')">Hapus</button>
            </td>
        </tr>
        <?php 
            }
        } else {
            echo "<tr><td colspan=\'7\' style=\'text-align:center;\'>Tidak ada tugas</td></tr>";
        }
        ?>
    </tbody>
</table>

<!-- Add Task Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeAddModal">&times;</span>
        <h2>Tambah Tugas Baru</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="judul">Judul:</label>
                <input type="text" id="judul" name="judul" required>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi:</label>
                <textarea id="deskripsi" name="deskripsi" required></textarea>
            </div>
            <div class="form-group">
                <label for="tanggal_mulai">Tanggal Mulai:</label>
                <input type="date" id="tanggal_mulai" name="tanggal_mulai" required>
            </div>
            <div class="form-group">
                <label for="tanggal_selesai">Tanggal Selesai:</label>
                <input type="date" id="tanggal_selesai" name="tanggal_selesai" required>
            </div>
            <button type="submit" name="add" class="form-submit">Tambah Tugas</button>
        </form>
    </div>
</div>

<!-- Edit Task Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditModal">&times;</span>
        <h2>Edit Tugas</h2>
        <form method="post" action="">
            <input type="hidden" id="edit_id" name="id">
            <div class="form-group">
                <label for="edit_judul">Judul:</label>
                <input type="text" id="edit_judul" name="judul" required>
            </div>
            <div class="form-group">
                <label for="edit_deskripsi">Deskripsi:</label>
                <textarea id="edit_deskripsi" name="deskripsi" required></textarea>
            </div>
            <div class="form-group">
                <label for="edit_status">Status:</label>
                <select id="edit_status" name="status">
                    <option value="Belum Selesai">Belum Selesai</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>
            <div class="form-group">
                <label for="edit_tanggal_mulai">Tanggal Mulai:</label>
                <input type="date" id="edit_tanggal_mulai" name="tanggal_mulai" required>
            </div>
            <div class="form-group">
                <label for="edit_tanggal_selesai">Tanggal Selesai:</label>
                <input type="date" id="edit_tanggal_selesai" name="tanggal_selesai" required>
            </div>
            <button type="submit" name="update" class="form-submit">Update Tugas</button>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeDeleteModal">&times;</span>
        <h2>Konfirmasi Hapus</h2>
        <p>Apakah Anda yakin ingin menghapus tugas "<span id="delete_task_name"></span>"?</p>
        <form method="post" action="">
            <input type="hidden" id="delete_id" name="id">
            <button type="submit" name="delete" class="form-submit" style="background-color: #e74c3c;">Hapus</button>
            <button type="button" id="cancelDelete" class="form-submit" style="background-color: #7f8c8d; margin-top: 10px;">Batal</button>
        </form>
    </div>
</div>
';

// Save the pages files
file_put_contents('pages/login.php', $loginContent);
file_put_contents('pages/register.php', $registerContent);
file_put_contents('pages/dashboard.php', $dashboardContent);
?>