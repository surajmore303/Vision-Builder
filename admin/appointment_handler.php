<?php
require_once '../config.php';

// Check admin session
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update_status') {
    $appointment_id = intval($_POST['appointment_id'] ?? 0);
    $status = trim($_POST['status'] ?? '');
    
    if ($appointment_id <= 0 || !in_array($status, ['approved', 'rejected'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $stmt->execute([$status, $appointment_id]);
        
        echo json_encode(['success' => true, 'message' => "Appointment $status successfully"]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to update appointment']);
    }
}
?>