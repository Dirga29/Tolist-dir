<?php
session_start();
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "todo_list";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['user_id']) && isset($_GET['search'])) {
    $user_id = $_SESSION['user_id'];
    $search = trim($_GET['search']);
    
    if (!empty($search)) {
        $search = mysqli_real_escape_string($conn, $search);
        $sql = "SELECT * FROM tasks WHERE user_id = $user_id AND (judul LIKE '%$search%' OR deskripsi LIKE '%$search%') ORDER BY id DESC";
    } else {
        $sql = "SELECT * FROM tasks WHERE user_id = $user_id ORDER BY id DESC";
    }
    
    $result = $conn->query($sql);
    $tasks = array();
    $counter = 1;
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $row['no'] = $counter++;
            $row['tanggal_mulai'] = date('d-m-Y', strtotime($row['tanggal_mulai']));
            $row['tanggal_selesai'] = date('d-m-Y', strtotime($row['tanggal_selesai']));
            $tasks[] = $row;
        }
    }
    
    echo json_encode($tasks);
}
?> 