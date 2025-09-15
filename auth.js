// Form switching functionality
function switchToRegister() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    // Slide out login form to the left
    loginForm.classList.add('slide-out-left');
    
    setTimeout(() => {
        loginForm.classList.remove('active', 'slide-out-left');
        registerForm.classList.add('active', 'slide-in-right');
        
        setTimeout(() => {
            registerForm.classList.remove('slide-in-right');
        }, 500);
    }, 250);
}

function switchToLogin() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    // Slide out register form to the right
    registerForm.classList.add('slide-out-right');
    
    setTimeout(() => {
        registerForm.classList.remove('active', 'slide-out-right');
        loginForm.classList.add('active', 'slide-in-left');
        
        setTimeout(() => {
            loginForm.classList.remove('slide-in-left');
        }, 500);
    }, 250);
}

// Form validation and submission
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.auth-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const isLogin = form.closest('#loginForm') !== null;
            const inputs = form.querySelectorAll('input[required]');
            let isValid = true;
            
            // Reset previous error states
            inputs.forEach(input => {
                input.style.borderColor = 'transparent';
            });
            
            // Validate all required fields
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = '#e74c3c';
                    input.style.background = '#ffeaea';
                }
            });
            
            if (!isValid) {
                showNotification('Please fill in all required fields', 'error');
                return;
            }
            
            // Email validation
            const emailInput = form.querySelector('input[type="email"]');
            if (emailInput) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailInput.value)) {
                    showNotification('Please enter a valid email address', 'error');
                    emailInput.style.borderColor = '#e74c3c';
                    emailInput.style.background = '#ffeaea';
                    return;
                }
            }
            
            // Password confirmation for registration
            if (!isLogin) {
                const passwordInputs = form.querySelectorAll('input[type="password"]');
                if (passwordInputs.length >= 2) {
                    const password = passwordInputs[0].value;
                    const confirmPassword = passwordInputs[1].value;
                    
                    if (password !== confirmPassword) {
                        showNotification('Passwords do not match', 'error');
                        passwordInputs[1].style.borderColor = '#e74c3c';
                        passwordInputs[1].style.background = '#ffeaea';
                        return;
                    }
                    
                    if (password.length < 6) {
                        showNotification('Password must be at least 6 characters long', 'error');
                        passwordInputs[0].style.borderColor = '#e74c3c';
                        passwordInputs[0].style.background = '#ffeaea';
                        return;
                    }
                }
            }
            
            // Phone number validation for registration
            if (!isLogin) {
                const phoneInput = form.querySelector('input[type="tel"]');
                if (phoneInput) {
                    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
                    if (!phoneRegex.test(phoneInput.value.replace(/\s/g, ''))) {
                        showNotification('Please enter a valid phone number', 'error');
                        phoneInput.style.borderColor = '#e74c3c';
                        phoneInput.style.background = '#ffeaea';
                        return;
                    }
                }
            }
            
            // Show loading state
            const submitBtn = form.querySelector('.btn-primary');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Processing...';
            submitBtn.disabled = true;
            
            // Prepare form data
            const formData = new FormData(form);
            formData.append('action', isLogin ? 'login' : 'register');
            
            // Submit to server
            fetch('auth.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect || 'account.php';
                    }, 1000);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('An error occurred. Please try again.', 'error');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    });
    
    // Social login handlers
    const socialBtns = document.querySelectorAll('.social-btn');
    socialBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const platform = this.classList.contains('google') ? 'Google' : 
                           this.classList.contains('facebook') ? 'Facebook' : 'LinkedIn';
            
            showNotification(`${platform} login coming soon...`, 'info');
        });
    });
});

// Input focus effects
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input');
    
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.background = '#f0e6d2';
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.style.background = '#e7d7c0';
            }
        });
        
        input.addEventListener('input', function() {
            // Reset error state when user starts typing
            this.style.borderColor = 'transparent';
            this.style.background = '#f0e6d2';
        });
    });
});

// Notification System
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-message">${message}</span>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10001;
        background: ${type === 'success' ? '#27ae60' : type === 'error' ? '#e74c3c' : '#3498db'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        animation: slideInRight 0.3s ease-out;
        max-width: 400px;
        word-wrap: break-word;
    `;
    
    const notificationContent = notification.querySelector('.notification-content');
    notificationContent.style.cssText = `
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    `;
    
    const closeButton = notification.querySelector('.notification-close');
    closeButton.style.cssText = `
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0;
        line-height: 1;
    `;
    
    // Add to document
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = 'slideOutRight 0.3s ease-out forwards';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Add CSS animations for notifications
const notificationStyles = document.createElement('style');
notificationStyles.textContent = `
    @keyframes slideInRight {
        0% {
            transform: translateX(100%);
            opacity: 0;
        }
        100% {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        0% {
            transform: translateX(0);
            opacity: 1;
        }
        100% {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(notificationStyles);