<?php
require_once 'config.php';
header('Content-Type: application/json');

echo json_encode([
    'logged_in' => isLoggedIn(),
    'user_type' => $_SESSION['user_type'] ?? null
]);
?>