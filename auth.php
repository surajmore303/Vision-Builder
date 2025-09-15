<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Please fill all fields']);
            exit;
        }
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Set session with longer duration (30 days)
                ini_set('session.gc_maxlifetime', 2592000); // 30 days
                session_set_cookie_params(2592000); // 30 days
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_type'] = $user['user_type'];
                
                // Update login count and last login
                $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW(), login_count = login_count + 1 WHERE id = ?");
                $updateStmt->execute([$user['id']]);
                
                // Log login session
                $sessionStmt = $pdo->prepare("INSERT INTO login_sessions (user_id, ip_address, user_agent, device_info) VALUES (?, ?, ?, ?)");
                $sessionStmt->execute([
                    $user['id'],
                    getUserIP(),
                    $_SERVER['HTTP_USER_AGENT'] ?? '',
                    $_SERVER['HTTP_USER_AGENT'] ?? ''
                ]);
                
                // Redirect based on user type
                $redirect = ($user['user_type'] === 'admin') ? 'admin/index.php' : 'account.php';
                echo json_encode(['success' => true, 'message' => 'Login successful', 'redirect' => $redirect]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Login failed']);
        }
    }
    
    elseif ($action === 'register') {
        $fullName = $_POST['fullName'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';
        
        if (empty($fullName) || empty($email) || empty($password) || empty($confirmPassword)) {
            echo json_encode(['success' => false, 'message' => 'Please fill all fields']);
            exit;
        }
        
        if ($password !== $confirmPassword) {
            echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
            exit;
        }
        
        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
            exit;
        }
        
        try {
            // Check if email exists
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $checkStmt->execute([$email]);
            
            if ($checkStmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Email already exists']);
                exit;
            }
            
            // Insert new user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertStmt = $pdo->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
            $insertStmt->execute([$fullName, $email, $hashedPassword]);
            
            echo json_encode(['success' => true, 'message' => 'Registration successful! Please login.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Registration failed']);
        }
    }
    
    elseif ($action === 'logout') {
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
    }
}
?>