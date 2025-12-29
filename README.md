# SQL-Injection

# 🛡️ SQL Injection- Installation Guide

## 📋 Introduction
Complete SQL Injection demo with:
- ✅ Detailed theory about SQLi and risks
- 🐛 Vulnerable Login Form (for learning)
- 🔒 Secure Login Form (Prepared Statements)
- 🎨 Beautiful, modern UI

## 📁 Directory Structure

```
sqli_demo/
├── assets/              # CSS and static resources
│   └── style.css
├── database/            # SQL database file
│   └── database.sql
├── includes/            # Configuration and includes
│   ├── config.php
│   └── config.php.example
├── pages/               # Demo pages
│   ├── vulnerable_login.php    # Vulnerable demo
│   ├── secure_login.php        # Secure demo
│   ├── union_attack.php        # Union-based SQLi
│   ├── union_guide.php         # Union Attack Guide
│   └── stacked_queries.php     # Stacked Queries Attack
├── utils/               # Utilities
│   ├── generate_hash.php       # Generate password hash
│   ├── setup_password.php      # Setup password
│   └── restore.php             # Restore database
├── index.html          # Home page
└── README.md          # This file
```

## 🚀 Installation

### Step 1: Configure Database
1. Copy `includes/config.php.example` to `includes/config.php`
2. Open `includes/config.php` and adjust database info:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        # Your MySQL username
define('DB_PASS', '');            # Your MySQL password
define('DB_NAME', 'sqli_demo');
```

### Step 2: Import Database
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create new database named `sqli_demo`
3. Import file `database/database.sql`

### Step 3: Setup Password Hash
**IMPORTANT**: After importing database, access:
```
http://localhost/sqli_demo/utils/setup_password.php
```
This will automatically create password hash and update the database.

### Step 4: Access Demo
Open browser and go to: `http://localhost/sqli_demo/`

## 🎯 Test Accounts

All accounts have password: **123456**

| Username | Role | Email |
|----------|------|-------|
| admin | admin | admin@company.com |
| john_doe | user | john@company.com |
| mary_smith | user | mary@company.com |
| bob_wilson | user | bob@company.com |

## 💣 Attack Instructions (For Learning Only!)

### On VULNERABLE Form (Unsafe):

#### 1. Bypass Login (Bypass password)
```
Username: admin' OR '1'='1' --
Password: (leave empty or any value)
```
**Result**: Successful login without knowing the password!

#### 2. Login to specific user
```
Username: john_doe' --
Password: (any value)
```
**Result**: Login to john_doe account

#### 3. Get all users
```
Username: ' OR 1=1 --
Password: (any value)
```
**Result**: Display information of ALL users

#### 4. Union Attack (Get data from other tables)
```
Username: admin' UNION SELECT 1,document_name,content,4,5,6,7 FROM sensitive_data --
Password: (any value)
```
**Result**: Get sensitive data from other tables!

### On SECURE Form (Safe):
Try ALL the above payloads → **ALL FAIL!** 🛡️

## 🔐 Security Measures in Secure Login

### 1. Prepared Statements
```php
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
```
✅ Parameters are separated from SQL statement

### 2. Input Validation
```php
if (strlen($username) < 3 || strlen($username) > 50) {
    die("Invalid username");
}
if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    die("Username can only contain letters, numbers and _");
}
```
✅ Check format and length

### 3. Password Hashing
```php
password_verify($password, $hashed_password)
```
✅ Password encrypted with bcrypt

### 4. Error Handling
```php
catch (Exception $e) {
    $error = "An error occurred";
    error_log($e->getMessage()); # Log to file
}
```
✅ No detailed SQL errors shown to user

## ⚠️ IMPORTANT WARNING

1. **FOR LEARNING ONLY**: This demo contains security vulnerabilities
2. **DO NOT deploy to production**: Only run on localhost
3. **ILLEGAL ACTIVITY**: Attacking real systems is against the law

## 📖 Reference Documentation

- [OWASP SQL Injection](https://owasp.org/www-community/attacks/SQL_Injection)
- [PHP Prepared Statements](https://www.php.net/manual/en/mysqli.quickstart.prepared-statements.php)
- [Password Hashing Best Practices](https://www.php.net/manual/en/function.password-hash.php)

## 🎓 Important Lessons

### ❌ NEVER do:
```php
$sql = "SELECT * FROM users WHERE username = '$username'";
```

### ✅ ALWAYS do:
```php
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
```

## 💡 Tips

- Use Network tab in Developer Tools to see request/response
- Observe SQL statements generated in the debug section
- Compare the difference between both forms to understand better

## 🤝 Contributing

If you have ideas to improve this demo, please create a pull request!

## 📝 License

MIT License - Free to use for educational purposes

---

**Happy learning! 🚀**

*Remember: With great power comes great responsibility!*
