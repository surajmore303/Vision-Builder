# Vision Builder - Real-time Login System

## Setup Instructions

1. **Database Setup**
   - Start XAMPP (Apache + MySQL)
   - Create database named `vision_builder` in phpMyAdmin
   - Run `setup_database.php` in browser to create tables and admin user

2. **Admin Access**
   - Email: `admin@visionbuilder.com`
   - Password: `admin123`

## Features Fixed

✅ **Splash Screen Issue**: Now shows only on first visit, not on every page load
✅ **Login Persistence**: Users stay logged in for 30 days
✅ **Navigation Flow**: Login redirects to account page, not direct to account tab
✅ **Contact Form**: Now saves submissions to database
✅ **Admin Panel**: Shows all registered users and contact form submissions

## How It Works

1. **First Visit**: Splash screen shows for 3 seconds
2. **Return Visits**: Direct access to website (no splash screen)
3. **Login**: Redirects to account page after successful login
4. **Session**: Maintains login for 30 days
5. **Contact Form**: Saves all submissions to database
6. **Admin Panel**: View users, login history, and contact messages

## File Structure

- `index.html` - Main website
- `login.html` - Login/Register page
- `account.php` - User account dashboard
- `admin/index.php` - Admin panel
- `auth.php` - Authentication handler
- `contact_handler.php` - Contact form handler
- `config.php` - Database configuration
- `setup_database.php` - Database initialization

## Usage

1. Visit website - splash screen shows once
2. Register/Login through login page
3. Access account dashboard after login
4. Admin can view all data in admin panel
5. Contact form submissions saved to database