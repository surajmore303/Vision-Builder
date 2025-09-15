<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.html');
    exit;
}

// Get user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get login history
$historyStmt = $pdo->prepare("SELECT * FROM login_sessions WHERE user_id = ? ORDER BY login_time DESC LIMIT 10");
$historyStmt->execute([$_SESSION['user_id']]);
$loginHistory = $historyStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Vision Builder</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="simple-mobile.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .account-container {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }
        .account-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            text-align: center;
        }
        .account-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        .account-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid #e7d7c0;
        }
        .account-card h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-label {
            font-weight: 600;
            color: #666;
        }
        .info-value {
            color: var(--primary-color);
        }
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        .logout-btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
        .login-history {
            max-height: 400px;
            overflow-y: auto;
        }
        .history-item {
            padding: 1rem;
            border: 1px solid #e7d7c0;
            border-radius: 10px;
            margin-bottom: 1rem;
            background: #f8f9fa;
        }
        @media (max-width: 768px) {
            .account-grid {
                grid-template-columns: 1fr;
            }
            .account-container {
                margin-top: 80px;
                padding: 0 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <div class="nav-container">
                <div class="nav-logo">
                    <img src="images/logo.png" alt="Vision Builder Logo" class="logo-img">
                    <span>Vision Builder</span>
                </div>
                <ul class="nav-menu">
                    <div class="mobile-logo">
                        <img src="images/logo.png" alt="Vision Builder" class="logo-img">
                        <span>Vision Builder</span>
                    </div>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="index.html#about">About</a></li>
                    <li><a href="services.html">Services</a></li>
                    <li><a href="team.html">Team</a></li>
                    <li><a href="experts.html">Experts</a></li>
                    <li><a href="index.html#contact">Contact</a></li>
                    <li><a href="account.php" class="login-btn">Account</a></li>
                    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
                    <li><a href="admin/index.php" style="background: #e74c3c; color: white; padding: 0.5rem 1rem; border-radius: 15px;">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="#" onclick="logout()" style="background: #e74c3c; color: white; padding: 0.5rem 1rem; border-radius: 15px; margin-left: 0.5rem;">Logout</a></li>
                </ul>
                <div class="hamburger" id="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>

    <div class="account-container">
        <div class="account-header">
            <h1><i class="fas fa-user-circle"></i> Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!</h1>
            <p>Manage your Vision Builder account</p>
        </div>

        <div class="account-grid">
            <div class="account-card">
                <h3><i class="fas fa-user"></i> Profile Information</h3>
                <div class="info-item">
                    <span class="info-label">Full Name:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['full_name']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Member Since:</span>
                    <span class="info-value"><?php echo date('M d, Y', strtotime($user['registration_date'])); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Last Login:</span>
                    <span class="info-value"><?php echo $user['last_login'] ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Never'; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Total Logins:</span>
                    <span class="info-value"><?php echo $user['login_count']; ?></span>
                </div>
                <button class="logout-btn" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>

            <div class="account-card">
                <h3><i class="fas fa-history"></i> Login History</h3>
                <div class="login-history">
                    <?php foreach ($loginHistory as $history): ?>
                    <div class="history-item">
                        <div><strong>Date:</strong> <?php echo date('M d, Y H:i:s', strtotime($history['login_time'])); ?></div>
                        <div><strong>IP:</strong> <?php echo htmlspecialchars($history['ip_address']); ?></div>
                        <div><strong>Device:</strong> <?php echo htmlspecialchars(substr($history['device_info'], 0, 50)) . '...'; ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                fetch('auth.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=logout'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'index.html';
                    }
                });
            }
        }
    </script>
</body>
</html>