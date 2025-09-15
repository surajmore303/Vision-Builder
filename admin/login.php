<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Vision Builder</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .admin-login-container {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .admin-header {
            margin-bottom: 2rem;
        }
        .admin-header i {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
        .admin-header h1 {
            color: #333;
            margin-bottom: 0.5rem;
        }
        .admin-header p {
            color: #666;
            font-size: 0.9rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
        }
        .form-group input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .login-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .back-link {
            margin-top: 2rem;
            display: block;
            color: #667eea;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .notification {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 10px;
            font-size: 0.9rem;
        }
        .notification.error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        .notification.success {
            background: #efe;
            color: #363;
            border: 1px solid #cfc;
        }
    </style>
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-header">
            <i class="fas fa-shield-alt"></i>
            <h1>Admin Panel</h1>
            <p>Secure Administrator Access</p>
        </div>
        
        <div id="notification"></div>
        
        <form id="adminLoginForm">
            <div class="form-group">
                <i class="fas fa-user-shield"></i>
                <input type="text" name="username" placeholder="Admin Username" required>
            </div>
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Admin Password" required>
            </div>
            <button type="submit" class="login-btn">
                <i class="fas fa-sign-in-alt"></i> Admin Login
            </button>
        </form>
        
        <a href="../index.html" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Website
        </a>
    </div>

    <script>
        document.getElementById('adminLoginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = this.querySelector('input[name="username"]').value;
            const password = this.querySelector('input[name="password"]').value;
            
            if (!username || !password) {
                showNotification('Please fill all fields', 'error');
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'admin_login');
            formData.append('username', username);
            formData.append('password', password);
            
            fetch('auth.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Login successful!', 'success');
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1000);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Login failed. Please try again.', 'error');
            });
        });
        
        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            notification.innerHTML = `<div class="notification ${type}">${message}</div>`;
            setTimeout(() => {
                notification.innerHTML = '';
            }, 5000);
        }
    </script>
</body>
</html>