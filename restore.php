<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restore Database - Khôi Phục Dữ Liệu</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <i class="fas fa-undo"></i>
                <h1>Khôi Phục Database</h1>
            </div>
            <a href="index.html" class="btn-back"><i class="fas fa-arrow-left"></i> Quay lại</a>
        </header>

        <div class="card" style="max-width: 900px; margin: 0 auto;">
            <h2><i class="fas fa-database"></i> Restore Dữ Liệu Sau Khi Bị Tấn Công</h2>

            <?php
            require_once 'config.php';
            
            $message = '';
            $error = '';
            
            if (isset($_POST['restore'])) {
                try {
                    // Khôi phục bảng users
                    $conn->query("DROP TABLE IF EXISTS users");
                    $conn->query("CREATE TABLE users (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        username VARCHAR(50) NOT NULL UNIQUE,
                        password VARCHAR(255) NOT NULL,
                        password_plain VARCHAR(50) NOT NULL COMMENT 'Password plaintext cho demo vulnerable',
                        email VARCHAR(100) NOT NULL,
                        full_name VARCHAR(100) NOT NULL,
                        role ENUM('user', 'admin') DEFAULT 'user',
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                    
                    $conn->query("INSERT INTO users (username, password, password_plain, email, full_name, role) VALUES
                        ('admin', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456', 'admin@company.com', 'Nguyễn Văn Admin', 'admin'),
                        ('john_doe', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456', 'john@company.com', 'John Doe', 'user'),
                        ('mary_smith', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456', 'mary@company.com', 'Mary Smith', 'user'),
                        ('bob_wilson', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456', 'bob@company.com', 'Bob Wilson', 'user')");
                    
                    // Khôi phục bảng products
                    $conn->query("DROP TABLE IF EXISTS products");
                    $conn->query("CREATE TABLE products (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(200) NOT NULL,
                        price DECIMAL(10,2) NOT NULL,
                        description TEXT,
                        stock INT DEFAULT 0,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                    
                    $conn->query("INSERT INTO products (name, price, description, stock) VALUES
                        ('iPhone 15 Pro', 29990000, 'Smartphone cao cấp từ Apple', 50),
                        ('Samsung Galaxy S24', 24990000, 'Flagship Android mới nhất', 35),
                        ('MacBook Pro M3', 45990000, 'Laptop chuyên nghiệp cho developer', 20),
                        ('iPad Air', 15990000, 'Máy tính bảng đa năng', 40),
                        ('AirPods Pro', 6990000, 'Tai nghe không dây chống ồn', 100)");
                    
                    // Khôi phục bảng sensitive_data
                    $conn->query("DROP TABLE IF EXISTS sensitive_data");
                    $conn->query("CREATE TABLE sensitive_data (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        document_name VARCHAR(200) NOT NULL,
                        content TEXT NOT NULL,
                        classification ENUM('public', 'confidential', 'top_secret') DEFAULT 'confidential',
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                    
                    $conn->query("INSERT INTO sensitive_data (document_name, content, classification) VALUES
                        ('Báo cáo tài chính Q4 2024', 'Doanh thu: 50 tỷ VNĐ, Lợi nhuận: 15 tỷ VNĐ', 'confidential'),
                        ('Danh sách khách hàng VIP', 'Công ty A: 0901234567, Công ty B: 0987654321', 'top_secret'),
                        ('Kế hoạch chiến lược 2025', 'Mở rộng thị trường Đông Nam Á, đầu tư 100 tỷ', 'top_secret'),
                        ('Thông tin nhân viên', 'Lương trung bình: 20 triệu, Số lượng: 150 người', 'confidential')");
                    
                    $message = "✅ Khôi phục thành công tất cả bảng và dữ liệu!";
                    
                } catch (Exception $e) {
                    $error = "❌ Lỗi khi khôi phục: " . $e->getMessage();
                }
            }
            
            if (isset($_POST['fix_password'])) {
                try {
                    $password = "123456";
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    
                    $stmt = $conn->prepare("UPDATE users SET password = ?, password_plain = ?");
                    $stmt->bind_param("ss", $hash, $password);
                    $stmt->execute();
                    
                    $message = "✅ Đã cập nhật password thành công! Tất cả tài khoản đều dùng password: 123456";
                } catch (Exception $e) {
                    $error = "❌ Lỗi: " . $e->getMessage();
                }
            }
            ?>

            <?php if ($message): ?>
                <div class="alert alert-success">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="highlight-box warning">
                <i class="fas fa-info-circle"></i>
                <div>
                    <strong>Chức năng:</strong>
                    <ul style="margin-top: 10px;">
                        <li>Khôi phục toàn bộ bảng: users, products, sensitive_data</li>
                        <li>Import lại dữ liệu mẫu</li>
                        <li>Reset password về 123456</li>
                    </ul>
                </div>
            </div>

            <form method="POST" style="margin-top: 30px;">
                <button type="submit" name="restore" class="btn btn-success" style="width: 100%; padding: 20px; font-size: 1.2rem;">
                    <i class="fas fa-database"></i> KHÔI PHỤC TOÀN BỘ DATABASE
                </button>
            </form>

            <form method="POST" style="margin-top: 15px;">
                <button type="submit" name="fix_password" class="btn" style="width: 100%; background: var(--warning); color: white;">
                    <i class="fas fa-key"></i> Chỉ Fix Password (nếu sai mật khẩu)
                </button>
            </form>

            <div style="margin-top: 30px;">
                <h3><i class="fas fa-terminal"></i> Hoặc chạy SQL thủ công:</h3>
                <div class="copy-box">
                    <h4>Copy và chạy trong phpMyAdmin:</h4>
                    <textarea readonly onclick="this.select()" style="width: 100%; height: 200px; font-family: monospace; padding: 10px;">-- Khôi phục bảng users
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    password_plain VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (username, password, password_plain, email, full_name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456', 'admin@company.com', 'Nguyễn Văn Admin', 'admin'),
('john_doe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456', 'john@company.com', 'John Doe', 'user');

-- Khôi phục bảng products
DROP TABLE IF EXISTS products;
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    stock INT DEFAULT 0
);

INSERT INTO products (name, price, description, stock) VALUES
('iPhone 15 Pro', 29990000, 'Smartphone cao cấp từ Apple', 50),
('Samsung Galaxy S24', 24990000, 'Flagship Android mới nhất', 35),
('MacBook Pro M3', 45990000, 'Laptop chuyên nghiệp cho developer', 20);</textarea>
                </div>
            </div>

            <?php
            // Hiển thị trạng thái các bảng
            echo "<div style='margin-top: 30px;'>";
            echo "<h3><i class='fas fa-list'></i> Trạng Thái Database:</h3>";
            echo "<div class='result-table'>";
            echo "<table>";
            echo "<thead><tr><th>Bảng</th><th>Trạng Thái</th><th>Số Bản Ghi</th></tr></thead>";
            echo "<tbody>";
            
            $tables = ['users', 'products', 'sensitive_data'];
            foreach ($tables as $table) {
                $result = $conn->query("SHOW TABLES LIKE '$table'");
                if ($result && $result->num_rows > 0) {
                    $count_result = $conn->query("SELECT COUNT(*) as total FROM $table");
                    $count = $count_result->fetch_assoc()['total'];
                    echo "<tr>";
                    echo "<td><strong>$table</strong></td>";
                    echo "<td><span class='badge badge-info'>✅ Tồn tại</span></td>";
                    echo "<td>$count bản ghi</td>";
                    echo "</tr>";
                } else {
                    echo "<tr>";
                    echo "<td><strong>$table</strong></td>";
                    echo "<td><span class='badge badge-danger'>❌ Không tồn tại</span></td>";
                    echo "<td>-</td>";
                    echo "</tr>";
                }
            }
            
            echo "</tbody></table>";
            echo "</div>";
            echo "</div>";
            ?>

            <div style="margin-top: 30px; text-align: center;">
                <a href="index.html" class="btn btn-success">
                    <i class="fas fa-home"></i> Quay Về Trang Chủ
                </a>
            </div>
        </div>
    </div>
</body>
</html>
