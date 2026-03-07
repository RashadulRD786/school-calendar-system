# 🎓 SK Saujana Utama - School Event Calendar Management System

<div align="center">

![PHP Version](https://img.shields.io/badge/PHP-7.4+-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)
![Status](https://img.shields.io/badge/Status-Production%20Ready-success.svg)

**A comprehensive web-based event management system designed for educational institutions to efficiently manage, track, and coordinate school events with role-based access control and real-time notifications.**

[Features](#-features) • [Tech Stack](#-tech-stack) • [Installation](#-installation) • [Project Structure](#-project-structure) • [Database Schema](#-database-schema) • [Usage](#-usage) • [API Documentation](#-api-documentation)

</div>

---

## 📋 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Project Structure](#-project-structure)
- [Database Schema](#-database-schema)
- [Usage Guide](#-usage-guide)
- [API Documentation](#-api-documentation)
- [Security Features](#-security-features)
- [Screenshots](#-screenshots)
- [Contributing](#-contributing)
- [License](#-license)
- [Author](#-author)

---

## 🎯 Overview

The **SK Saujana Utama School Event Calendar Management System** is a full-stack web application that streamlines event management for educational institutions. The system provides administrators and teachers with an intuitive interface to create, manage, and track school events while ensuring seamless communication through email notifications and in-app alerts.

### Key Highlights

- ✅ **Role-Based Access Control** - Separate dashboards for Administrators and Teachers
- ✅ **Real-Time Notifications** - Instant email and in-app notifications for new events
- ✅ **Excel Integration** - Bulk import/export capabilities for event data
- ✅ **Advanced Reporting** - Generate comprehensive reports with date range and status filters
- ✅ **Responsive Design** - Modern, mobile-friendly user interface
- ✅ **Secure Authentication** - Password hashing and secure session management

---

## ✨ Features

### 🔐 Authentication & Authorization
- Secure login system with email and password
- Role-based access control (Admin/Teacher)
- Password reset functionality via email
- Automatic password migration from plain text to hashed format
- Session management

### 📅 Event Management
- **Create Events** - Add new events with comprehensive details:
  - Event name, date, day, time, location
  - Multiple teacher involvement selection
  - Person in charge assignment
  - Unit categorization (PENGURUSAN, KURIKULUM, HAL EHWAL MURID, etc.)
  - Status tracking (COMPLETE/NOT-COMPLETE)
- **Edit Events** - Update event details with validation
- **Delete Events** - Single or bulk deletion by date range
- **View Events** - Interactive calendar view with month/year navigation
- **Event Details Panel** - Side panel showing events for selected dates

### 📧 Notification System
- **Email Notifications** - Automated emails sent to all users when new events are created
- **In-App Notifications** - Real-time notification dropdown with unread count badge
- **Notification Management** - Mark notifications as read, view event details from notifications
- **Auto-Refresh** - Notifications update every second for real-time updates

### 📊 Reporting & Analytics
- **Excel Export** - Generate detailed event reports in Excel format
- **Date Range Filtering** - Filter events by custom date ranges
- **Status Filtering** - Filter by completion status (Complete/Non-Complete/Any)
- **Comprehensive Reports** - Include all event details in exported reports

### 👥 User Management
- **Add Users** - Create new user accounts with role assignment
- **Edit Users** - Update user information and roles
- **Delete Users** - Remove user accounts with confirmation
- **User Listing** - View all registered users

### 📥 Data Import/Export
- **Excel Import** - Bulk import events from Excel files (.xlsx, .xls)
- **Excel Export** - Export events to Excel with all details
- **Data Validation** - Ensures data integrity during import

---

## 🛠 Tech Stack

### Backend
- **PHP 7.4+** - Server-side scripting
- **MySQL 5.7+** - Relational database management
- **MySQLi** - Database connectivity with prepared statements

### Frontend
- **HTML5** - Markup language
- **CSS3** - Styling and responsive design
- **JavaScript (ES6+)** - Client-side interactivity
- **jQuery 3.6.0** - DOM manipulation and AJAX
- **Select2 4.1.0** - Enhanced multi-select dropdowns

### Libraries & Dependencies
- **PHPMailer** - Email sending functionality
- **PHPSpreadsheet 4.2+** - Excel file processing
- **Composer** - Dependency management

### Development Tools
- **Composer** - PHP package manager
- **Git** - Version control

---

## 🚀 Installation

### Prerequisites

Before installing, ensure you have the following installed:
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (for dependency management)
- A Gmail account (for email notifications)

### Step 1: Clone the Repository

```bash
git clone https://github.com/yourusername/SKSU_school_calendar_system.git
cd SKSU_school_calendar_system/school-calendar-system
```

### Step 2: Install Dependencies

```bash
composer install
```

This will install:
- PHPSpreadsheet
- PHPMailer (if not already included)

### Step 3: Database Setup

1. Create a MySQL database:
```sql
CREATE DATABASE school_system;
```

2. Import the database schema (create tables):
```sql
-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255),
    password_hash VARCHAR(255),
    role ENUM('Admin', 'Teacher') NOT NULL,
    name VARCHAR(255),
    reset_token VARCHAR(64),
    reset_expires INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Events table
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    day VARCHAR(20),
    date DATE NOT NULL,
    time TIME NOT NULL,
    location VARCHAR(255),
    involvement TEXT,
    person_in_charge VARCHAR(255),
    unit VARCHAR(100),
    status ENUM('COMPLETE', 'NOT-COMPLETE') DEFAULT 'NOT-COMPLETE',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Notifications table
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    event_name VARCHAR(255),
    event_date DATE,
    event_time TIME,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Teacher list table
CREATE TABLE teacher_list (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL
);
```

### Step 4: Configuration

1. **Database Configuration** (`db_connection.php`):
```php
$host = "localhost";
$username = "root";
$password = ""; // Your MySQL password
$database = "school_system";
$port = 3307; // Adjust if needed
```

2. **Email Configuration** (`send_event_notification.php`):
```php
$mail->Username = 'your-email@gmail.com';
$mail->Password = 'your-app-specific-password'; // Gmail App Password
```

> **Note:** For Gmail, you need to generate an [App Password](https://support.google.com/accounts/answer/185833) instead of using your regular password.

### Step 5: Web Server Configuration

1. **Apache**: Ensure mod_rewrite is enabled
2. **Nginx**: Configure proper routing
3. Point your web server document root to the `school-calendar-system` directory

### Step 6: Set Permissions

```bash
chmod -R 755 school-calendar-system
chmod -R 777 uploads # If you have an uploads directory
```

---

## ⚙️ Configuration

### Environment Variables

Create a configuration file or update existing files with your settings:

**Database Settings:**
- Host: `localhost`
- Port: `3307` (default: `3306`)
- Database: `school_system`
- Username: `root`
- Password: Your MySQL password

**Email Settings:**
- SMTP Host: `smtp.gmail.com`
- SMTP Port: `587`
- SMTP Security: `TLS`
- Email: Your Gmail address
- App Password: Gmail App Password

**Application Settings:**
- Base URL: `http://localhost/school-calendar-system/`
- Session Timeout: Configure in PHP settings
- Notification Refresh Rate: 1000ms (1 second)

---

## 📁 Project Structure

```
school-calendar-system/
│
├── 📄 index.html                 # Login page
├── 📄 login.php                  # Login authentication handler
├── 📄 style.css                  # Login page styles
│
├── 📄 admin-dashboard1.html      # Admin dashboard
├── 📄 teacher-dashboard1.html    # Teacher dashboard
├── 📄 admin1-styles.css          # Dashboard styles
├── 📄 admin_script.js            # Dashboard JavaScript logic
│
├── 📄 db_connection.php          # Database connection configuration
│
├── 📄 add_event.php              # Create new event
├── 📄 edit_event.php             # Update existing event
├── 📄 delete_event.php           # Delete single event
├── 📄 get_events.php             # Fetch all events (JSON API)
├── 📄 get_event_by_id.php        # Fetch single event (JSON API)
│
├── 📄 get_teachers.php           # Fetch teacher list (JSON API)
├── 📄 get_notifications.php      # Fetch notifications (JSON API)
├── 📄 mark_notifications_read.php # Mark notifications as read
├── 📄 send_event_notification.php # Email notification handler
│
├── 📄 import_events.php          # Excel import handler
├── 📄 report.php                 # Excel export handler
├── 📄 report.html                # Report generation page
├── 📄 report.js                  # Report page JavaScript
├── 📄 report.css                 # Report page styles
│
├── 📄 delete_events.html         # Bulk delete interface
├── 📄 delete_events.php          # Bulk delete handler
├── 📄 delete_events.js           # Bulk delete JavaScript
├── 📄 delete_events.css          # Bulk delete styles
│
├── 📄 forgot_password.html        # Password reset request page
├── 📄 send_reset_link.php        # Send password reset email
├── 📄 reset_password.php         # Password reset handler
├── 📄 update_password.php         # Update password handler
│
├── 📂 user-management/           # User management module
│   ├── user-management.php       # User listing page
│   ├── add-user1.html            # Add user form
│   ├── add-user.php              # Add user handler
│   ├── edit-user.html            # Edit user form
│   ├── edit-user.php             # Edit user handler
│   ├── update-user.php           # Update user handler
│   ├── delete-user.php           # Delete user handler
│   ├── get-users.php             # Fetch users (JSON API)
│   └── *.css, *.js               # Module-specific assets
│
├── 📂 PHPMailer-master/          # PHPMailer library
│   └── src/                      # PHPMailer source files
│
├── 📂 vendor/                    # Composer dependencies
│   ├── phpoffice/                # PHPSpreadsheet library
│   └── autoload.php              # Composer autoloader
│
├── 📄 composer.json              # PHP dependencies
├── 📄 composer.lock              # Locked dependency versions
│
└── 📄 README.md                  # This file
```

---

## 🗄️ Database Schema

### Tables Overview

#### `users`
Stores user account information and authentication data.

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT | Primary key, auto-increment |
| `email` | VARCHAR(255) | Unique email address |
| `password` | VARCHAR(255) | Legacy plain text password |
| `password_hash` | VARCHAR(255) | Hashed password (bcrypt) |
| `role` | ENUM | User role: 'Admin' or 'Teacher' |
| `name` | VARCHAR(255) | User's full name |
| `reset_token` | VARCHAR(64) | Password reset token |
| `reset_expires` | INT | Token expiration timestamp |
| `created_at` | TIMESTAMP | Account creation timestamp |

#### `events`
Stores all school event information.

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT | Primary key, auto-increment |
| `name` | VARCHAR(255) | Event name |
| `day` | VARCHAR(20) | Day of week (e.g., "Monday") |
| `date` | DATE | Event date |
| `time` | TIME | Event time |
| `location` | VARCHAR(255) | Event location |
| `involvement` | TEXT | Comma-separated teacher names |
| `person_in_charge` | VARCHAR(255) | Person responsible |
| `unit` | VARCHAR(100) | School unit (PENGURUSAN, KURIKULUM, etc.) |
| `status` | ENUM | 'COMPLETE' or 'NOT-COMPLETE' |
| `created_at` | TIMESTAMP | Event creation timestamp |

#### `notifications`
Stores in-app notification records.

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT | Primary key, auto-increment |
| `event_id` | INT | Foreign key to events table |
| `event_name` | VARCHAR(255) | Event name (denormalized) |
| `event_date` | DATE | Event date (denormalized) |
| `event_time` | TIME | Event time (denormalized) |
| `is_read` | TINYINT(1) | Read status (0 = unread, 1 = read) |
| `created_at` | TIMESTAMP | Notification creation timestamp |

#### `teacher_list`
Stores list of teachers for involvement selection.

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT | Primary key, auto-increment |
| `full_name` | VARCHAR(255) | Teacher's full name |

---

## 📖 Usage Guide

### For Administrators

1. **Login**
   - Navigate to the login page
   - Enter your admin email and password
   - You'll be redirected to the Admin Dashboard

2. **Create Event**
   - Click the "+ Add Event" button
   - Fill in all required fields
   - Select multiple teachers for involvement
   - Choose the appropriate unit and status
   - Click "Confirm" to save

3. **Import Events from Excel**
   - Click "Import Excel" button
   - Select an Excel file (.xlsx or .xls)
   - Ensure the file has columns: Name, Day, Date, Time, Location, Involvement, Person in Charge, Unit
   - Events will be automatically imported

4. **Generate Reports**
   - Navigate to "Reports" in the sidebar
   - Select date range (From/To)
   - Choose status filter (Optional)
   - Click "Export" to download Excel report

5. **Bulk Delete Events**
   - Navigate to "Delete Events" in the sidebar
   - Select date range
   - Click "Delete" and confirm

6. **Manage Users**
   - Navigate to "Admin" in the sidebar
   - Add, edit, or delete users
   - Assign roles (Admin/Teacher)

### For Teachers

1. **Login**
   - Use your teacher credentials
   - Access the Teacher Dashboard

2. **View Events**
   - Browse the calendar view
   - Click on dates to see events
   - Click event labels to view details

3. **Create Events**
   - Similar to admin, but with teacher permissions
   - Can add events to the calendar

4. **View Reports**
   - Access reports section
   - Export events within date ranges

### Password Reset

1. Click "Forgot Password" on login page
2. Enter your email address
3. Check your email for reset link
4. Click the link (valid for 30 minutes)
5. Enter new password and confirm

---

## 🔌 API Documentation

### Authentication Endpoints

#### `POST /login.php`
Authenticate user and create session.

**Request:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "redirect": "admin-dashboard1.html"
}
```

### Event Endpoints

#### `GET /get_events.php`
Retrieve all events.

**Response:**
```json
[
  {
    "id": 1,
    "name": "Annual Sports Day",
    "day": "Saturday",
    "date": "2024-03-15",
    "time": "09:00:00",
    "location": "School Field",
    "involvement": "John Doe, Jane Smith",
    "person_in_charge": "Mr. Ahmad",
    "unit": "KOKURIKULUM",
    "status": "NOT-COMPLETE"
  }
]
```

#### `GET /get_event_by_id.php?id={event_id}`
Retrieve single event by ID.

**Response:**
```json
[
  {
    "id": 1,
    "name": "Annual Sports Day",
    ...
  }
]
```

#### `POST /add_event.php`
Create new event.

**Request:**
```
name=Event Name
day=Monday
date=2024-03-15
time=09:00
location=Location
involvement=Teacher1,Teacher2
person_in_charge=Person Name
unit=KURIKULUM
status=NOT-COMPLETE
```

#### `POST /edit_event.php`
Update existing event.

**Request:**
```
id=1
name=Updated Event Name
...
```

#### `POST /delete_event.php`
Delete single event.

**Request:**
```
id=1
```

### Notification Endpoints

#### `GET /get_notifications.php`
Retrieve notifications.

**Response:**
```json
{
  "notifications": [
    {
      "id": 1,
      "eventName": "Annual Sports Day",
      "eventDate": "2024-03-15",
      "eventTime": "09:00:00",
      "read": false,
      "eventId": 1
    }
  ],
  "unread_count": 5
}
```

#### `POST /mark_notifications_read.php`
Mark all notifications as read.

**Response:**
```json
{
  "success": true
}
```

### User Management Endpoints

#### `GET /get_teachers.php`
Retrieve teacher list.

**Response:**
```json
[
  {
    "id": 1,
    "full_name": "John Doe"
  }
]
```

#### `GET /user-management/get-users.php`
Retrieve all users.

**Response:**
```json
[
  {
    "id": 1,
    "email": "admin@school.edu"
  }
]
```

---

## 🔒 Security Features

### Authentication Security
- ✅ Password hashing using `password_hash()` with `PASSWORD_DEFAULT`
- ✅ Automatic migration from plain text to hashed passwords
- ✅ Secure session management
- ✅ Password reset tokens with expiration (30 minutes)
- ✅ SQL injection prevention using prepared statements

### Data Protection
- ✅ Input validation and sanitization
- ✅ XSS prevention with `htmlspecialchars()`
- ✅ CSRF protection (recommended for production)
- ✅ Secure password reset flow

### Best Practices
- ✅ Prepared statements for all database queries
- ✅ Error handling without exposing sensitive information
- ✅ Secure email configuration
- ✅ Role-based access control



## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Coding Standards
- Follow PSR-12 coding standards for PHP
- Use meaningful variable and function names
- Add comments for complex logic
- Ensure all features are tested before submission

---

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👤 Author

**Your Name**

- GitHub: [RashadulRD786](https://github.com/RashadulRD786)
- Email: nafisrashadul@gmail.com
- LinkedIn: [Rashadul Nafis Riyad](https://www.linkedin.com/in/rashadul-nafis-riyad/))

---

## 🙏 Acknowledgments

- **PHPMailer** - For email functionality
- **PHPSpreadsheet** - For Excel file processing
- **Select2** - For enhanced dropdowns
- **jQuery** - For DOM manipulation
- SK Saujana Utama for the project opportunity

---

## 📞 Support

For support, email nafisrashadul@gmail.com or create an issue in the repository.

---

<div align="center">

**Made with ❤️ for SK Saujana Utama**

⭐ Star this repo if you find it helpful!

</div>


