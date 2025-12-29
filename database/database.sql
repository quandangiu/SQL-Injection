-- =====================================================
-- SQL INJECTION DEMO DATABASE
-- =====================================================

-- Tạo database
CREATE DATABASE IF NOT EXISTS sqli_demo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sqli_demo;

-- Xóa bảng nếu đã tồn tại
DROP TABLE IF EXISTS users;

-- Tạo bảng users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    password_plain VARCHAR(50) NOT NULL COMMENT 'Password plaintext cho demo vulnerable',
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm dữ liệu mẫu
-- Password cho tất cả user là: "123456"
-- password_plain: dùng cho demo vulnerable (SQLi)
-- password: hash dùng cho demo secure (Prepared Statements)
INSERT INTO users (username, password, password_plain, email, full_name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456', 'admin@company.com', 'Nguyễn Văn Admin', 'admin'),
('john_doe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456', 'john@company.com', 'John Doe', 'user'),
('mary_smith', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456', 'mary@company.com', 'Mary Smith', 'user'),
('bob_wilson', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456', 'bob@company.com', 'Bob Wilson', 'user');

-- Tạo bảng products (cho demo Union Attack)
DROP TABLE IF EXISTS products;
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm dữ liệu sản phẩm
INSERT INTO products (name, price, description, stock) VALUES
('iPhone 15 Pro', 29990000, 'Smartphone cao cấp từ Apple', 50),
('Samsung Galaxy S24', 24990000, 'Flagship Android mới nhất', 35),
('MacBook Pro M3', 45990000, 'Laptop chuyên nghiệp cho developer', 20),
('iPad Air', 15990000, 'Máy tính bảng đa năng', 40),
('AirPods Pro', 6990000, 'Tai nghe không dây chống ồn', 100);

-- Tạo bảng sensitive_data (dữ liệu nhạy cảm của doanh nghiệp)
DROP TABLE IF EXISTS sensitive_data;
CREATE TABLE sensitive_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_name VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    classification ENUM('public', 'confidential', 'top_secret') DEFAULT 'confidential',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm dữ liệu nhạy cảm
INSERT INTO sensitive_data (document_name, content, classification) VALUES
('Báo cáo tài chính Q4 2024', 'Doanh thu: 50 tỷ VNĐ, Lợi nhuận: 15 tỷ VNĐ', 'confidential'),
('Danh sách khách hàng VIP', 'Công ty A: 0901234567, Công ty B: 0987654321', 'top_secret'),
('Kế hoạch chiến lược 2025', 'Mở rộng thị trường Đông Nam Á, đầu tư 100 tỷ', 'top_secret'),
('Thông tin nhân viên', 'Lương trung bình: 20 triệu, Số lượng: 150 người', 'confidential');

-- Hiển thị kết quả
SELECT 'Database created successfully!' as status;
SELECT * FROM users;
SELECT * FROM products;
SELECT * FROM sensitive_data;
