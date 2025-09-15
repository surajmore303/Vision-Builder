 # Vision Builder - Complete Web Application with Admin Dashboard

## 🚀 Project Overview

Vision Builder is a comprehensive web application featuring user authentication, expert appointment booking, contact management, and a professional admin dashboard with glassmorphism design.

## ✨ Key Features

### 🌐 **Frontend Features**
- **Responsive Design** - Works on all devices
- **Smart Splash Screen** - Shows only on first visit
- **Modern UI** - Professional design with animations
- **Expert Profiles** - Alumni experts with booking system
- **Contact Form** - Direct communication with admin
- **Persistent Login** - 30-day session management

### 👥 **User Management**
- **User Registration** - Secure account creation
- **Login System** - Email/password authentication
- **Account Dashboard** - User profile and login history
- **Session Persistence** - Stay logged in for 30 days
- **Expert Booking** - Book appointments with alumni

### 🎛️ **Admin Dashboard**
- **Glassmorphism Design** - Modern transparent UI
- **Real-time Statistics** - Users, logins, appointments
- **User Management** - View all registered users
- **Appointment Control** - Approve/reject booking requests
- **Contact Management** - View and manage messages
- **Login Activity** - Track user sessions and IPs

## 🛠️ Database Setup

### **Method 1: Automatic Setup (Recommended)**
1. Start XAMPP (Apache + MySQL)
2. Visit: `http://localhost/vision%20builder/setup_database.php`
3. Database and tables will be created automatically

### **Method 2: Manual Setup**
1. Open phpMyAdmin
2. Create database: `vision_builder`
3. Import: `vision_builder.sql`

### **Database Tables Created:**
- `users` - User accounts and profiles
- `login_sessions` - Login history and tracking
- `contact_submissions` - Contact form messages
- `appointments` - Expert booking requests

## 🔐 Admin Access

### **Admin Login**
- **URL**: `http://localhost/vision%20builder/admin/login.php`
- **Username**: `visionadmin`
- **Password**: `vision123`

### **Admin Features**
- View all users and statistics
- Manage appointment requests
- Handle contact form submissions
- Track login activity and IPs
- Professional dashboard interface

## 📁 File Structure

```
vision builder/
├── index.html              # Main website
├── login.html              # User login/register
├── account.php             # User dashboard
├── experts.html            # Expert profiles & booking
├── services.html           # Services page
├── team.html              # Team page
├── auth.php               # User authentication
├── booking_handler.php    # Appointment booking
├── contact_handler.php    # Contact form handler
├── config.php             # Database configuration
├── setup_database.php     # Database initialization
├── check_session.php      # Session validation
├── script.js              # Main JavaScript
├── auth.js                # Authentication JS
├── styles.css             # Main styles
├── auth.css               # Authentication styles
├── admin/
│   ├── index.php          # Admin dashboard
│   ├── login.php          # Admin login
│   ├── auth.php           # Admin authentication
│   └── appointment_handler.php # Appointment management
└── images/                # Website images
```

## 🎯 How It Works

### **User Journey**
1. **First Visit** → Splash screen (3 seconds)
2. **Registration** → Create account via login page
3. **Login** → Access account dashboard
4. **Expert Booking** → Book appointments with alumni
5. **Contact** → Send messages to admin

### **Admin Workflow**
1. **Admin Login** → Separate admin authentication
2. **Dashboard** → View statistics and data
3. **User Management** → Monitor registrations
4. **Appointment Control** → Approve/reject requests
5. **Message Handling** → Respond to contacts

## 🔧 Configuration

### **Database Settings** (`config.php`)
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'vision_builder');
```

### **Admin Credentials** (`admin/auth.php`)
```php
$admin_username = 'visionadmin';
$admin_password = 'vision123';
```

## 🚀 Quick Start

1. **Setup XAMPP** - Start Apache & MySQL
2. **Copy Files** - Place in `htdocs/vision builder/`
3. **Run Setup** - Visit `setup_database.php`
4. **Access Website** - `http://localhost/vision%20builder/`
5. **Admin Panel** - `http://localhost/vision%20builder/admin/login.php`

## 📱 Responsive Design

- **Desktop** - Full featured layout
- **Tablet** - Optimized grid system
- **Mobile** - Touch-friendly interface
- **All Devices** - Consistent experience

## 🎨 Design Features

- **Glassmorphism** - Modern transparent effects
- **Gradient Backgrounds** - Beautiful color schemes
- **Smooth Animations** - Professional transitions
- **Icon Integration** - FontAwesome icons
- **Typography** - Poppins font family

## 🔒 Security Features

- **Password Hashing** - Secure user passwords
- **Session Management** - Secure login sessions
- **SQL Injection Protection** - Prepared statements
- **XSS Prevention** - Input sanitization
- **Admin Separation** - Isolated admin system

---

**Vision Builder** - Building your vision into reality with innovative digital solutions! 🌟
