# Intelligent ID Scanning & Authentication System

A complete system for scanning student IDs, authenticating identities, and maintaining logs of all authentication attempts.

## 📋 Features

- **QR Code Scanning** - Scan student ID QR codes for quick authentication
- **Student Management** - Add, edit, and manage student records
- **Authentication Logging** - Track all authentication attempts with timestamps
- **Admin Dashboard** - View statistics and manage system data
- **Secure Authentication** - Admin login system with session management
- **MySQL Database** - Persistent data storage with XAMPP

## 🚀 Quick Start

### Prerequisites

- XAMPP (Apache + MySQL + PHP)
- Node.js (v14+)
- npm

### Installation

1. **Start XAMPP**
   - Open XAMPP Control Panel
   - Start Apache and MySQL services

2. **Import Database**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Click "Import" tab
   - Select `schema.sql`
   - Click "Go"

3. **Place project files**
   - Copy project folder to `C:\xampp\htdocs\thesis` (Windows)
   - Or `/Applications/XAMPP/htdocs/thesis` (Mac)
   - Or `/opt/lampp/htdocs/thesis` (Linux)

4. **Install Node dependencies**

   ```bash
   cd thesis
   npm install
   ```

5. **Start the server**

   ```bash
   npm start
   ```

6. **Access the system**
   - Main app: `http://localhost/thesis`
   - Admin login: `http://localhost/thesis/admin-login.html`
   - QR scanner: `http://localhost/thesis/qr-code%20generator.html`

## 📂 Project Structure

```
thesis/
├── api/
│   ├── login.php              # Admin login
│   ├── get-logs.php           # Retrieve authentication logs
│   ├── get-students.php       # List students
│   └── delete-student.php     # Delete student
├── authenticate.php            # QR code authentication endpoint
├── scan_student.php            # Scan student details
├── save_student.php            # Save/update student
├── db_config.php               # Database configuration
├── middleware.php              # Security middleware
├── index.html                  # Main page
├── admin-login.html            # Admin login page
├── dashboard.html              # Admin dashboard
├── qr-code generator.html      # QR code generator
├── schema.sql                  # Database schema
├── server.js                   # Node.js backend
├── package.json                # Dependencies
├── .env                        # Environment variables
└── README.md                   # This file
```

## 🔑 Default Admin Credentials

```
Username: admin
Password: admin123
```

⚠️ **IMPORTANT**: Change these credentials in production!

## 📡 API Endpoints

### Authentication

- **POST** `/api/login.php` - Admin login
  ```json
  {
    "username": "admin",
    "password": "admin123"
  }
  ```

### Students

- **GET** `/api/get-students.php?limit=100&offset=0&search=name` - List students
- **POST** `/save_student.php` - Create/update student
- **POST** `/api/delete-student.php` - Delete student

### Logs

- **GET** `/api/get-logs.php?limit=50&offset=0&status=Authenticated` - Authentication logs
- **POST** `/authenticate.php` - Record authentication
- **POST** `/scan_student.php` - Scan student details

## 🛡️ Security Features

- **Prepared Statements** - SQL injection protection
- **Session Management** - Admin authentication
- **Input Validation** - Middleware validation
- **Error Handling** - Secure error messages
- **Audit Logging** - Track admin actions

## ⚙️ Configuration

### Database (db_config.php)

```php
$db = new mysqli('localhost', 'root', '', 'intelligent_id_db');
```

Edit these values if your MySQL credentials differ:

- `localhost` - MySQL host
- `root` - MySQL username
- `` - MySQL password (empty by default)
- `intelligent_id_db` - Database name

### Environment Variables (.env)

```
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=intelligent_id_db
PORT=3000
```

## 📊 Database Schema

### Tables

- **Admin** - Admin user accounts
- **Student** - Student information
- **ParentsContacts** - Parent contact details
- **AuthenticationLog** - Authentication attempt records
- **SystemUsers** - System user accounts
- **ViolationRecords** - Violation records
- **AuditLog** - Admin action audit trail (optional)

## 🔧 Troubleshooting

### Can't connect to database

- Ensure MySQL is running in XAMPP
- Check database credentials in `db_config.php`
- Verify `intelligent_id_db` database exists

### QR code not scanning

- Ensure camera permissions are granted
- Check browser console for errors
- Try a different QR code generator

### API returns 404

- Verify project files are in correct htdocs folder
- Ensure Apache is running
- Check file paths in requests

### Node server not starting

- Run `npm install` to install dependencies
- Check for port 3000 conflicts
- Verify Node.js is installed: `node --version`

## 📝 Notes

- Default test credentials in database are provided in `schema.sql`
- All passwords should be hashed in production
- Update `.env` file with production values
- Enable HTTPS for production deployment
- Regularly backup database

## 🤝 Support

For issues or questions, check:

1. Console errors (F12 in browser)
2. Server logs (terminal output)
3. Database connection status in phpMyAdmin

## 📄 License

This project is for educational purposes.

---

**Happy Scanning!** 📱✨
