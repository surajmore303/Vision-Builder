// Splash Screen Animation and Main Website Display
document.addEventListener('DOMContentLoaded', function() {
    const splashScreen = document.getElementById('splash-screen');
    const mainWebsite = document.getElementById('main-website');
    
    // Check if this is first visit
    const hasVisited = localStorage.getItem('visionBuilderVisited');
    
    // Check if user is logged in
    fetch('check_session.php')
        .then(response => response.json())
        .then(data => {
            // Update navigation based on login status
            updateNavigation(data.logged_in);
            
            if (hasVisited || data.logged_in) {
                // Skip splash screen for returning users or logged in users
                splashScreen.style.display = 'none';
                mainWebsite.classList.remove('hidden');
                mainWebsite.style.opacity = '1';
                initScrollAnimations();
            } else {
                // Show splash screen for 3 seconds for first-time visitors
                setTimeout(() => {
                    splashScreen.style.display = 'none';
                    mainWebsite.classList.remove('hidden');
                    mainWebsite.style.opacity = '1';
                    mainWebsite.style.animation = 'websiteSlideIn 2s ease-out forwards';
                    initScrollAnimations();
                    // Mark as visited
                    localStorage.setItem('visionBuilderVisited', 'true');
                }, 3000);
            }
        })
        .catch(() => {
            // If session check fails, show splash screen only for first visit
            if (!hasVisited) {
                setTimeout(() => {
                    splashScreen.style.display = 'none';
                    mainWebsite.classList.remove('hidden');
                    mainWebsite.style.opacity = '1';
                    mainWebsite.style.animation = 'websiteSlideIn 2s ease-out forwards';
                    initScrollAnimations();
                    localStorage.setItem('visionBuilderVisited', 'true');
                }, 3000);
            } else {
                splashScreen.style.display = 'none';
                mainWebsite.classList.remove('hidden');
                mainWebsite.style.opacity = '1';
                initScrollAnimations();
            }
        });
    
    // Initialize all functionality
    initNavigation();
    initTestimonialSlider();
    initContactForm();
    initScrollEffects();
});

// Update navigation based on login status
function updateNavigation(isLoggedIn) {
    const authBtn = document.getElementById('authBtn');
    if (authBtn) {
        if (isLoggedIn) {
            authBtn.textContent = 'Account';
            authBtn.href = 'account.php';
            authBtn.onclick = null;
        } else {
            authBtn.textContent = 'Login';
            authBtn.href = 'login.html';
            authBtn.onclick = null;
        }
    }
}

// Logout function
function logout() {
    fetch('auth.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=logout'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Logged out successfully', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    });
}

// Navigation Functionality
function initNavigation() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    const navLinks = document.querySelectorAll('.nav-menu a');
    
    // Mobile menu toggle
    if (hamburger) {
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
    }
    
    // Close mobile menu when clicking on links
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
        });
    });
    
    // Smooth scrolling for navigation links
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');
            if (href.startsWith('#')) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
}

// Header scroll effect
function initScrollEffects() {
    const header = document.querySelector('.header');
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
            header.style.background = 'rgba(255, 255, 255, 0.98)';
            header.style.boxShadow = '0 2px 20px rgba(0,0,0,0.1)';
        } else {
            header.style.background = 'rgba(255, 255, 255, 0.95)';
            header.style.boxShadow = 'none';
        }
    });
}

// Scroll animations for elements
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Animate service cards
    const serviceCards = document.querySelectorAll('.service-card');
    serviceCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(50px)';
        card.style.transition = `all 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });
    
    // Animate team members
    const teamMembers = document.querySelectorAll('.team-member');
    teamMembers.forEach((member, index) => {
        member.style.opacity = '0';
        member.style.transform = 'translateY(50px)';
        member.style.transition = `all 0.6s ease ${index * 0.1}s`;
        observer.observe(member);
    });
    
    // Animate stats
    const stats = document.querySelectorAll('.stat');
    stats.forEach((stat, index) => {
        stat.style.opacity = '0';
        stat.style.transform = 'translateY(30px)';
        stat.style.transition = `all 0.6s ease ${index * 0.2}s`;
        observer.observe(stat);
        
        // Animate numbers
        const numberElement = stat.querySelector('h4');
        const finalNumber = parseInt(numberElement.textContent);
        
        stat.addEventListener('animationstart', () => {
            animateNumber(numberElement, 0, finalNumber, 2000);
        });
    });
}

// Number animation for stats
function animateNumber(element, start, end, duration) {
    const startTime = performance.now();
    
    function updateNumber(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        const current = Math.floor(start + (end - start) * progress);
        element.textContent = current + '+';
        
        if (progress < 1) {
            requestAnimationFrame(updateNumber);
        }
    }
    
    requestAnimationFrame(updateNumber);
}

// Testimonial Slider
function initTestimonialSlider() {
    const testimonials = document.querySelectorAll('.testimonial');
    let currentTestimonial = 0;
    
    function showTestimonial(index) {
        testimonials.forEach((testimonial, i) => {
            testimonial.classList.toggle('active', i === index);
        });
    }
    
    function nextTestimonial() {
        currentTestimonial = (currentTestimonial + 1) % testimonials.length;
        showTestimonial(currentTestimonial);
    }
    
    // Auto-rotate testimonials every 5 seconds
    setInterval(nextTestimonial, 5000);
}

// Contact Form Handling
function initContactForm() {
    const contactForm = document.querySelector('.contact-form');
    
    contactForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        // Get form data
        const name = contactForm.querySelector('input[name="name"]').value;
        const email = contactForm.querySelector('input[name="email"]').value;
        const message = contactForm.querySelector('textarea[name="message"]').value;
        
        // Simple validation
        if (!name || !email || !message) {
            showNotification('Please fill in all fields', 'error');
            return;
        }
        
        // Submit to server
        const formData = new FormData();
        formData.append('action', 'contact');
        formData.append('name', name);
        formData.append('email', email);
        formData.append('message', message);
        
        fetch('contact_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Message sent successfully! We\'ll get back to you soon.', 'success');
                contactForm.reset();
            } else {
                showNotification('Failed to send message. Please try again.', 'error');
            }
        })
        .catch(error => {
            showNotification('Failed to send message. Please try again.', 'error');
        });
    });
}

// Modal Functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    // Close modal when clicking outside
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal(modalId);
        }
    });
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

function switchModal(currentModalId, targetModalId) {
    closeModal(currentModalId);
    setTimeout(() => {
        openModal(targetModalId);
    }, 300);
}

// Auth Form Handling
document.addEventListener('DOMContentLoaded', function() {
    const authForms = document.querySelectorAll('.auth-form');
    
    authForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const isLogin = form.closest('#loginModal') !== null;
            const formType = isLogin ? 'Login' : 'Registration';
            
            // Simple validation
            const inputs = form.querySelectorAll('input[required]');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = '#e74c3c';
                } else {
                    input.style.borderColor = '#e0e0e0';
                }
            });
            
            if (!isValid) {
                showNotification('Please fill in all required fields', 'error');
                return;
            }
            
            // Email validation
            const emailInput = form.querySelector('input[type="email"]');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailInput.value)) {
                showNotification('Please enter a valid email address', 'error');
                emailInput.style.borderColor = '#e74c3c';
                return;
            }
            
            // Password confirmation for registration
            if (!isLogin) {
                const password = form.querySelector('input[type="password"]').value;
                const confirmPassword = form.querySelectorAll('input[type="password"]')[1].value;
                
                if (password !== confirmPassword) {
                    showNotification('Passwords do not match', 'error');
                    return;
                }
                
                if (password.length < 6) {
                    showNotification('Password must be at least 6 characters long', 'error');
                    return;
                }
            }
            
            // Simulate successful submission
            showNotification(`${formType} successful!`, 'success');
            form.reset();
            
            // Close modal after successful submission
            setTimeout(() => {
                const modalId = isLogin ? 'loginModal' : 'registerModal';
                closeModal(modalId);
            }, 1500);
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

// Keyboard navigation for modals
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('.modal[style*="block"]');
        openModals.forEach(modal => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        });
    }
});

// Smooth scroll for all anchor links
document.addEventListener('click', (e) => {
    if (e.target.tagName === 'A' && e.target.getAttribute('href').startsWith('#')) {
        e.preventDefault();
        const target = document.querySelector(e.target.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }
});

// Add loading animation to buttons
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('btn-primary') || e.target.classList.contains('btn-secondary')) {
        const button = e.target;
        const originalText = button.textContent;
        
        // Add loading state
        button.style.position = 'relative';
        button.style.color = 'transparent';
        
        const loader = document.createElement('div');
        loader.style.cssText = `
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        `;
        
        button.appendChild(loader);
        
        // Remove loading state after 1 second
        setTimeout(() => {
            button.removeChild(loader);
            button.style.color = '';
            button.textContent = originalText;
        }, 1000);
    }
});

// Add spin animation for loading
const spinStyles = document.createElement('style');
spinStyles.textContent = `
    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }
`;
document.head.appendChild(spinStyles);

// Parallax effect for hero section
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const heroImage = document.querySelector('.hero-image img');
    
    if (heroImage) {
        heroImage.style.transform = `translateY(${scrolled * 0.1}px) rotateY(${scrolled * 0.01}deg)`;
    }
});

// Add hover effects to service cards
document.addEventListener('DOMContentLoaded', function() {
    const serviceCards = document.querySelectorAll('.service-card');
    
    serviceCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-15px) rotateY(5deg)';
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0) rotateY(0deg)';
        });
    });
});

// Global hamburger menu function for all pages
function toggleMenu() {
    const navMenu = document.querySelector('.nav-menu');
    const hamburger = document.querySelector('.hamburger');
    
    if (navMenu && hamburger) {
        navMenu.classList.toggle('active');
        hamburger.classList.toggle('active');
    }
}

// Enhanced Mobile Menu
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    
    if (hamburger) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            
            if (navMenu.style.display === 'flex') {
                navMenu.style.display = 'none';
                navMenu.classList.remove('active');
            } else {
                navMenu.style.display = 'flex';
                navMenu.classList.add('active');
            }
        });
    }
    
    // Close menu when clicking links (only on mobile)
    const navLinks = document.querySelectorAll('.nav-menu a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            // Only close menu if screen width is mobile size
            if (window.innerWidth <= 768) {
                navMenu.style.display = 'none';
                navMenu.classList.remove('active');
                hamburger.classList.remove('active');
            }
        });
    });
});

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Vision Builder website loaded successfully!');
    
    // Add some interactive elements
    const logo = document.querySelector('.nav-logo i');
    if (logo) {
        logo.addEventListener('click', () => {
            logo.style.animation = 'eyeBlink 0.5s ease-in-out';
            setTimeout(() => {
                logo.style.animation = '';
            }, 500);
        });
    }
});