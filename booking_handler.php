<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'book_appointment') {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login to book appointment']);
        exit;
    }
    
    $expert_name = trim($_POST['expert_name'] ?? '');
    $preferred_date = trim($_POST['preferred_date'] ?? '');
    $time_slot = trim($_POST['time_slot'] ?? '');
    $discussion_topic = trim($_POST['discussion_topic'] ?? '');
    $user_id = $_SESSION['user_id'];
    
    if (empty($expert_name) || empty($preferred_date) || empty($time_slot) || empty($discussion_topic)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all fields']);
        exit;
    }
    
    try {
        // Create appointments table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS appointments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            expert_name VARCHAR(100) NOT NULL,
            preferred_date VARCHAR(50) NOT NULL,
            time_slot VARCHAR(50) NOT NULL,
            discussion_topic TEXT NOT NULL,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )");
        
        $stmt = $pdo->prepare("INSERT INTO appointments (user_id, expert_name, preferred_date, time_slot, discussion_topic) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $expert_name, $preferred_date, $time_slot, $discussion_topic]);
        
        echo json_encode(['success' => true, 'message' => 'Appointment booked successfully! Admin will review your request.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to book appointment']);
    }
}
?>