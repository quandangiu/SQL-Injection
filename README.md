# 🛡️ SQL Injection Demo - Hướng Dẫn Cài Đặt

## 📋 Giới thiệu
Demo hoàn chỉnh về SQL Injection với:
- ✅ Lý thuyết chi tiết về SQLi và rủi ro
- 🐛 Form đăng nhập DỄ BỊ TẤN CÔNG (để học)
- 🔒 Form đăng nhập AN TOÀN (Prepared Statements)
- 🎨 Giao diện cực đẹp, hiện đại

## 🚀 Cài đặt

### Bước 1: Import Database
1. Mở phpMyAdmin: `http://localhost/phpmyadmin`
2. Tạo database mới tên `sqli_demo` (hoặc import sẽ tự tạo)
3. Import file `database.sql`

### Bước 2: Setup Password Hash
**QUAN TRỌNG**: Sau khi import database, truy cập:
```
http://localhost/sqli_demo/setup_password.php
```
File này sẽ tự động tạo password hash đúng và cập nhật vào database.

### Bước 3: Cấu hình Database (nếu cần)
Mở file `config.php` và điều chỉnh nếu cần:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Username MySQL của bạn
define('DB_PASS', '');            // Password MySQL của bạn
define('DB_NAME', 'sqli_demo');
```

### Bước 4: Truy cập Demo
Mở trình duyệt và vào: `http://localhost/sqli_demo/`

## 🎯 Tài khoản test

Tất cả tài khoản đều có password: **123456**

| Username | Role | Email |
|----------|------|-------|
| admin | admin | admin@company.com |
| john_doe | user | john@company.com |
| mary_smith | user | mary@company.com |
| bob_wilson | user | bob@company.com |

## 💣 Hướng dẫn tấn công (chỉ dùng để học!)

### Trên form VULNERABLE (Không an toàn):

#### 1. Bypass Login (Bỏ qua mật khẩu)
```
Username: admin' OR '1'='1' --
Password: (để trống hoặc bất kỳ)
```
**Kết quả**: Đăng nhập thành công mà không cần biết password!

#### 2. Đăng nhập với user cụ thể
```
Username: john_doe' --
Password: (bất kỳ)
```
**Kết quả**: Đăng nhập vào tài khoản john_doe

#### 3. Lấy tất cả user
```
Username: ' OR 1=1 --
Password: (bất kỳ)
```
**Kết quả**: Hiển thị thông tin TẤT CẢ người dùng

#### 4. Union Attack (Lấy dữ liệu từ bảng khác)
```
Username: admin' UNION SELECT 1,document_name,content,4,5,6,7 FROM sensitive_data --
Password: (bất kỳ)
```
**Kết quả**: Lấy được dữ liệu nhạy cảm từ bảng khác!

### Trên form SECURE (An toàn):
Thử TẤT CẢ các payload trên → **ĐỀU THẤT BẠI!** 🛡️

## 📚 Cấu trúc thư mục

```
sqli_demo/
├── index.html              # Trang chủ với lý thuyết SQLi
├── vulnerable_login.php    # Form dễ bị tấn công
├── secure_login.php        # Form an toàn với Prepared Statements
├── config.php              # Cấu hình database
├── style.css               # Giao diện đẹp
├── database.sql            # File SQL để import
└── README.md               # File này
```

## 🔐 Biện pháp bảo mật trong Secure Login

### 1. Prepared Statements
```php
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
```
✅ Tham số được tách biệt khỏi câu lệnh SQL

### 2. Input Validation
```php
if (strlen($username) < 3 || strlen($username) > 50) {
    die("Username không hợp lệ");
}
if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    die("Username chỉ được chứa chữ, số và _");
}
```
✅ Kiểm tra định dạng và độ dài

### 3. Password Hashing
```php
password_verify($password, $hashed_password)
```
✅ Mật khẩu được mã hóa bằng bcrypt

### 4. Error Handling
```php
catch (Exception $e) {
    $error = "Có lỗi xảy ra";
    error_log($e->getMessage()); // Log vào file
}
```
✅ Không hiển thị lỗi SQL chi tiết cho user

## ⚠️ CẢNH BÁO QUAN TRỌNG

1. **CHỈ dùng để học tập**: Demo này chứa code có lỗ hổng bảo mật
2. **KHÔNG triển khai lên server thật**: Chỉ chạy trên localhost
3. **Hành vi bất hợp pháp**: Tấn công hệ thống thực là vi phạm pháp luật

## 📖 Tài liệu tham khảo

- [OWASP SQL Injection](https://owasp.org/www-community/attacks/SQL_Injection)
- [PHP Prepared Statements](https://www.php.net/manual/en/mysqli.quickstart.prepared-statements.php)
- [Password Hashing Best Practices](https://www.php.net/manual/en/function.password-hash.php)

## 🎓 Bài học quan trọng

### ❌ KHÔNG BAO GIỜ làm:
```php
$sql = "SELECT * FROM users WHERE username = '$username'";
```

### ✅ LUÔN LUÔN làm:
```php
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
```

## 💡 Tips

- Sử dụng tab Network trong Developer Tools để xem request/response
- Quan sát câu lệnh SQL được tạo ra ở phần debug
- So sánh sự khác biệt giữa 2 form để hiểu rõ hơn

## 🤝 Đóng góp

Nếu có ý tưởng cải thiện demo, hãy tạo pull request!

## 📝 License

MIT License - Tự do sử dụng cho mục đích giáo dục

---

**Chúc bạn học tốt! 🚀**

*Remember: With great power comes great responsibility!*
