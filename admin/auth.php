<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'admin_login') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all fields']);
        exit;
    }
    
    // Admin credentials - change these as needed
    $admin_username = 'visionadmin';
    $admin_password = 'vision123';
    
    // Debug - remove after testing
    error_log("Username: '$username', Password: '$password'");
    error_log("Expected: '$admin_username', '$admin_password'");
    
    if ($username === $admin_username && $password === $admin_password) {
        // Set admin session
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['admin_login_time'] = time();
        
        echo json_encode(['success' => true, 'message' => 'Admin login successful']);
    } else {
        echo json_encode(['success' => false, 'message' => "Invalid credentials. Got: '$username'/'$password'"]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'admin_logout') {
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_username']);
    unset($_SESSION['admin_login_time']);
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
}
?>