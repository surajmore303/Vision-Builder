<?php
require_once '../config.php';

// Check admin session
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Get statistics
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'user'")->fetchColumn();
$totalLogins = $pdo->query("SELECT COUNT(*) FROM login_sessions")->fetchColumn();
$todayLogins = $pdo->query("SELECT COUNT(*) FROM login_sessions WHERE DATE(login_time) = CURDATE()")->fetchColumn();
$activeUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active' AND user_type = 'user'")->fetchColumn();
$totalContacts = $pdo->query("SELECT COUNT(*) FROM contact_submissions")->fetchColumn();

// Create appointments table if not exists
try {
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
    $totalAppointments = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
    $pendingAppointments = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'")->fetchColumn();
} catch (Exception $e) {
    $totalAppointments = 0;
    $pendingAppointments = 0;
}

// Get recent users
$recentUsers = $pdo->query("SELECT * FROM users WHERE user_type = 'user' ORDER BY registration_date DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

// Get recent logins
$recentLogins = $pdo->query("
    SELECT u.full_name, u.email, ls.login_time, ls.ip_address 
    FROM login_sessions ls 
    JOIN users u ON ls.user_id = u.id 
    ORDER BY ls.login_time DESC LIMIT 15
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vision Builder - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .admin-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            color: #2c3e50;
            padding: 1.5rem 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo i {
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: rgba(255, 255, 255, 0.1);
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            backdrop-filter: blur(10px);
        }

        .user-info span {
            font-weight: 500;
            color: #2c3e50;
        }

        .logout-btn {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(238, 90, 36, 0.3);
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(238, 90, 36, 0.4);
        }

        .container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .dashboard-nav {
            background: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--border);
            margin-bottom: 2rem;
        }

        .nav-tabs {
            display: flex;
            gap: 2rem;
        }

        .nav-tab {
            padding: 0.75rem 1.5rem;
            border: none;
            background: none;
            color: var(--text-secondary);
            font-weight: 500;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease;
        }

        .nav-tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .trend-up { color: var(--success); }
        .trend-down { color: var(--danger); }

        .stat-card.users::before { background: linear-gradient(135deg, #3498db, #2980b9); }
        .stat-card.logins::before { background: linear-gradient(135deg, #27ae60, #229954); }
        .stat-card.today::before { background: linear-gradient(135deg, #f39c12, #e67e22); }
        .stat-card.active::before { background: linear-gradient(135deg, #e74c3c, #c0392b); }
        .stat-card.contacts::before { background: linear-gradient(135deg, #9b59b6, #8e44ad); }
        .stat-card.appointments::before { background: linear-gradient(135deg, #e67e22, #d35400); }

        .stat-card i {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #2c3e50, #34495e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #666;
            font-size: 1rem;
            font-weight: 500;
        }

        .stat-change {
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-top: 0.5rem;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        .panel-header {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 1.5rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .panel-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .panel-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: white;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-sm:hover {
            background: var(--light);
        }

        .panel-content {
            padding: 2rem;
            max-height: 500px;
            overflow-y: auto;
        }

        .panel-content::-webkit-scrollbar {
            width: 6px;
        }

        .panel-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .panel-content::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 10px;
        }

        .data-row {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .data-row:hover {
            background: var(--light);
        }

        .data-row:last-child {
            border-bottom: none;
        }

        .data-main {
            flex: 1;
        }

        .data-actions {
            display: flex;
            gap: 0.5rem;
        }

        .data-title {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .data-subtitle {
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .data-meta {
            color: var(--text-secondary);
            font-size: 0.75rem;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .status-active { background: #dcfce7; color: #166534; }
        .status-new { background: #fef3c7; color: #92400e; }
        .status-read { background: #e0f2fe; color: #0e7490; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #dcfce7; color: #166534; }
        .status-rejected { background: #fee2e2; color: #991b1b; }

        .approve-btn, .reject-btn {
            padding: 0.375rem 0.75rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .approve-btn {
            background: var(--success);
            color: white;
        }

        .approve-btn:hover {
            background: #059669;
        }

        .reject-btn {
            background: var(--danger);
            color: white;
        }

        .reject-btn:hover {
            background: #dc2626;
        }

        .full-width-panel {
            grid-column: 1 / -1;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .container {
                padding: 0 1rem;
            }
            
            .nav-tabs {
                overflow-x: auto;
                padding-bottom: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <header class="admin-header">
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-cog"></i> Vision Builder Admin
            </div>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                <button onclick="adminLogout()" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="stats-grid">
            <div class="stat-card users">
                <i class="fas fa-users"></i>
                <div class="stat-number"><?php echo $totalUsers; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card logins">
                <i class="fas fa-sign-in-alt"></i>
                <div class="stat-number"><?php echo $totalLogins; ?></div>
                <div class="stat-label">Total Logins</div>
            </div>
            <div class="stat-card today">
                <i class="fas fa-calendar-day"></i>
                <div class="stat-number"><?php echo $todayLogins; ?></div>
                <div class="stat-label">Today's Logins</div>
            </div>
            <div class="stat-card active">
                <i class="fas fa-user-check"></i>
                <div class="stat-number"><?php echo $activeUsers; ?></div>
                <div class="stat-label">Active Users</div>
            </div>
            <div class="stat-card contacts">
                <i class="fas fa-envelope"></i>
                <div class="stat-number"><?php echo $totalContacts; ?></div>
                <div class="stat-label">Contact Messages</div>
            </div>
            <div class="stat-card appointments">
                <i class="fas fa-calendar-check"></i>
                <div class="stat-number"><?php echo $pendingAppointments; ?></div>
                <div class="stat-label">Pending Appointments</div>
            </div>
        </div>

        <div class="content-grid">
            <div class="panel">
                <div class="panel-header">
                    <i class="fas fa-user-plus"></i> Recent Registrations
                </div>
                <div class="panel-content">
                    <?php foreach ($recentUsers as $user): ?>
                        <div class="user-item" style="background: rgba(255, 255, 255, 0.7); margin-bottom: 1rem; padding: 1.5rem; border-radius: 15px; border: 1px solid rgba(255, 255, 255, 0.3); transition: all 0.3s ease; display: flex; justify-content: space-between; align-items: center;">
                            <div class="user-info-item" style="flex: 1;">
                                <div class="user-name" style="font-weight: 600; color: #2c3e50; font-size: 1.1rem; margin-bottom: 0.3rem;"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                <div class="user-email" style="color: #666; font-size: 0.95rem; margin-bottom: 0.3rem;"><?php echo htmlspecialchars($user['email']); ?></div>
                                <div class="user-date" style="color: #999; font-size: 0.85rem;">Registered: <?php echo date('M d, Y H:i', strtotime($user['registration_date'])); ?></div>
                            </div>
                            <div class="status-badge status-active">Active</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <i class="fas fa-history"></i> Recent Login Activity
                </div>
                <div class="panel-content">
                    <?php foreach ($recentLogins as $login): ?>
                        <div class="login-item">
                            <div class="user-info-item">
                                <div class="user-name"><?php echo htmlspecialchars($login['full_name']); ?></div>
                                <div class="user-email"><?php echo htmlspecialchars($login['email']); ?></div>
                                <div class="login-time"><?php echo date('M d, Y H:i:s', strtotime($login['login_time'])); ?></div>
                                <div class="ip-address">IP: <?php echo htmlspecialchars($login['ip_address']); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        </div>
        
        <div class="content-grid">
            <div class="panel">
                <div class="panel-header">
                    <i class="fas fa-envelope"></i> Contact Form Submissions
                </div>
            <div class="panel-content">
                <?php
                $contactSubmissions = $pdo->query("SELECT * FROM contact_submissions ORDER BY submission_date DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($contactSubmissions as $contact):
                ?>
                    <div class="contact-item" style="background: rgba(255, 255, 255, 0.7); margin-bottom: 1rem; padding: 1.5rem; border-radius: 15px; border: 1px solid rgba(255, 255, 255, 0.3); transition: all 0.3s ease; display: flex; justify-content: space-between; align-items: flex-start;">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #2c3e50; font-size: 1.1rem; margin-bottom: 0.5rem;">
                                <?php echo htmlspecialchars($contact['name']); ?>
                            </div>
                            <div style="color: #666; font-size: 0.95rem; margin-bottom: 0.5rem;">
                                <?php echo htmlspecialchars($contact['email']); ?>
                            </div>
                            <div style="color: #333; margin-bottom: 0.5rem; line-height: 1.5;">
                                <?php echo htmlspecialchars($contact['message']); ?>
                            </div>
                            <div style="color: #999; font-size: 0.85rem;">
                                <?php echo date('M d, Y H:i:s', strtotime($contact['submission_date'])); ?>
                            </div>
                        </div>
                        <div class="status-badge <?php echo $contact['status'] === 'new' ? 'status-new' : 'status-read'; ?>">
                            <?php echo ucfirst($contact['status']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
            <div class="panel">
                <div class="panel-header">
                    <i class="fas fa-calendar-check"></i> Appointment Requests
                </div>
            <div class="panel-content">
                <?php 
                try {
                    $appointments = $pdo->query("
                        SELECT a.*, u.full_name, u.email 
                        FROM appointments a 
                        JOIN users u ON a.user_id = u.id 
                        ORDER BY a.booking_date DESC LIMIT 20
                    ")->fetchAll(PDO::FETCH_ASSOC);
                } catch (Exception $e) {
                    $appointments = [];
                }
                foreach ($appointments as $appointment): 
                ?>
                <div class="appointment-item" style="background: rgba(255, 255, 255, 0.7); margin-bottom: 1rem; padding: 1.5rem; border-radius: 15px; border: 1px solid rgba(255, 255, 255, 0.3); transition: all 0.3s ease; display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #2c3e50; font-size: 1.1rem; margin-bottom: 0.5rem;">
                            <?php echo htmlspecialchars($appointment['full_name']); ?> → <?php echo htmlspecialchars($appointment['expert_name']); ?>
                        </div>
                        <div style="color: #666; font-size: 0.95rem; margin-bottom: 0.5rem;">
                            📧 <?php echo htmlspecialchars($appointment['email']); ?>
                        </div>
                        <div style="color: #666; font-size: 0.95rem; margin-bottom: 0.5rem;">
                            📅 <?php echo htmlspecialchars($appointment['preferred_date']); ?> | ⏰ <?php echo htmlspecialchars($appointment['time_slot']); ?>
                        </div>
                        <div style="color: #333; margin-bottom: 0.5rem; line-height: 1.5;">
                            💬 <?php echo htmlspecialchars($appointment['discussion_topic']); ?>
                        </div>
                        <div style="color: #999; font-size: 0.85rem;">
                            📝 Booked: <?php echo date('M d, Y H:i:s', strtotime($appointment['booking_date'])); ?>
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.5rem;">
                        <div class="status-badge <?php echo 'status-' . $appointment['status']; ?>">
                            <?php echo ucfirst($appointment['status']); ?>
                        </div>
                        <?php if ($appointment['status'] === 'pending'): ?>
                        <div style="display: flex; gap: 0.5rem;">
                            <button class="approve-btn" onclick="updateAppointment(<?php echo $appointment['id']; ?>, 'approved')" style="background: linear-gradient(135deg, #27ae60, #229954); color: white; padding: 0.5rem 1rem; border: none; border-radius: 20px; cursor: pointer; font-size: 0.85rem; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);">
                                ✓ Approve
                            </button>
                            <button class="reject-btn" onclick="updateAppointment(<?php echo $appointment['id']; ?>, 'rejected')" style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; padding: 0.5rem 1rem; border: none; border-radius: 20px; cursor: pointer; font-size: 0.85rem; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);">
                                ✗ Reject
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            </div>
        </div>
    </div>
    
    <script>
        function adminLogout() {
            if (confirm('Are you sure you want to logout?')) {
                fetch('auth.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=admin_logout'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'login.php';
                    }
                });
            }
        }
        
        function switchTab(tab) {
            document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));
            event.target.classList.add('active');
        }
        
        function updateAppointment(appointmentId, status) {
            if (confirm(`Are you sure you want to ${status} this appointment?`)) {
                fetch('appointment_handler.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=update_status&appointment_id=${appointmentId}&status=${status}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to update appointment');
                    }
                });
            }
        }
    </script>
</body>

</html>