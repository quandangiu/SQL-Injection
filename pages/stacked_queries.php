<?php
// stacked_queries.php - DEMO STACKED QUERIES / DESTRUCTIVE ATTACK
// ⚠️ Mô phỏng tấn công phá hoại với nhiều câu lệnh SQL

require_once '../includes/config.php';

$error = '';
$warning = '';
$sql_query = '';
$result_data = [];
$search_term = '';
$attack_detected = false;
$attack_type = '';

if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
    
    // Phát hiện các từ khóa nguy hiểm
    $dangerous_keywords = ['DROP', 'DELETE', 'UPDATE', 'INSERT', 'CREATE', 'ALTER', 'TRUNCATE', 'EXEC'];
    
    foreach ($dangerous_keywords as $keyword) {
        if (stripos($search_term, $keyword) !== false) {
            $attack_detected = true;
            $attack_type = $keyword;
            break;
        }
    }
    
    // ❌ NGUY HIỂM: Nối chuỗi trực tiếp
    $sql = "SELECT name, price, description FROM products WHERE name LIKE '%$search_term%'";
    
    // Lưu câu query để hiển thị
    $sql_query = $sql;
    
    // 🔥 THỰC THI THỰC SỰ - Tách các câu lệnh và chạy từng cái
    if ($attack_detected) {
        // Tách các câu lệnh bằng dấu ;
        $statements = array_filter(array_map('trim', explode(';', $search_term)), function($s) {
            return !empty($s) && $s != '--' && !preg_match('/^--/', $s);
        });
        
        $warning = "🔥 <strong>ĐANG THỰC THI CÁC LỆNH PHÁ HOẠI...</strong><br><br>";
        
        foreach ($statements as $stmt) {
            try {
                // Thực thi từng câu lệnh
                $result = $conn->query($stmt);
                $warning .= "✅ Đã thực thi: <code>" . htmlspecialchars($stmt) . "</code><br>";
            } catch (Exception $e) {
                $warning .= "❌ Lỗi: <code>" . htmlspecialchars($stmt) . "</code> - " . $e->getMessage() . "<br>";
            }
        }
        
        $warning .= "<br>💀 <strong>DỮ LIỆU ĐÃ BỊ PHÁ HOẠI!</strong><br>";
        $warning .= "⚠️ Vào <a href='restore.php' style='color: #fcd34d; text-decoration: underline;'>restore.php</a> để khôi phục dữ liệu!";
        
    } else if ($attack_detected) {
        $warning = "🚨 PHÁT HIỆN TẤNG CÔNG PHÍA HOẠI: <strong>" . htmlspecialchars($attack_type) . "</strong><br>";
        $warning .= "⚠️ Nếu đây là hệ thống thực (SQL Server, PostgreSQL, PDO), dữ liệu của bạn đã BỊ PHÁ HOẠI!<br><br>";
        
        // Mô phỏng kết quả
        switch (strtoupper($attack_type)) {
            case 'DROP':
                $warning .= "💀 <strong>BẢNG ĐÃ BỊ XÓA!</strong><br>";
                $warning .= "📊 Tất cả dữ liệu trong bảng users/products đã biến mất vĩnh viễn!<br>";
                $warning .= "💸 Thiệt hại ước tính: Hàng triệu đô la + mất uy tín";
                break;
            case 'DELETE':
                $warning .= "🗑️ <strong>DỮ LIỆU ĐÃ BỊ XÓA!</strong><br>";
                $warning .= "📊 Tất cả bản ghi đã bị xóa khỏi bảng!<br>";
                $warning .= "⚠️ Nếu không có backup, dữ liệu mất vĩnh viễn!";
                break;
            case 'UPDATE':
                $warning .= "✏️ <strong>DỮ LIỆU ĐÃ BỊ THAY ĐỔI!</strong><br>";
                $warning .= "📊 Giá sản phẩm = 1đ, hoặc user thường → admin<br>";
                $warning .= "💰 Công ty có thể mất hàng tỷ đồng!";
                break;
            case 'INSERT':
                $warning .= "🚪 <strong>BACKDOOR ĐÃ ĐƯỢC TẠO!</strong><br>";
                $warning .= "👤 Tài khoản admin giả mạo đã được chèn vào database<br>";
                $warning .= "⚠️ Kẻ tấn công có thể quay lại bất cứ lúc nào!";
                break;
        }
    } else {
        // Chỉ thực thi query thông thường nếu không có tấn công
        try {
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $result_data[] = $row;
                }
            } else {
                $error = "❌ Không tìm thấy sản phẩm với từ khóa: " . htmlspecialchars($search_term);
            }
        } catch (Exception $e) {
            $error = "❌ Lỗi: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stacked Queries - SQL Injection Demo</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <i class="fas fa-bomb"></i>
                <h1>Stacked Queries Attack</h1>
            </div>
            <a href="index.html" class="btn-back"><i class="fas fa-arrow-left"></i> Quay lại</a>
        </header>

        <div class="login-container">
            <div class="login-card danger-card">
                <h2><i class="fas fa-search"></i> Tìm Kiếm Sản Phẩm</h2>
                <p style="color: var(--text-muted); margin-bottom: 20px;">Form này giả lập lỗ hổng Stacked Queries trên SQL Server/PostgreSQL</p>
                
                <?php if ($warning): ?>
                    <div class="alert alert-danger" style="border: 3px solid var(--danger); background: rgba(239,68,68,0.2); animation: pulse 2s infinite;">
                        <?php echo $warning; ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="GET" class="login-form">
                    <div class="form-group">
                        <label><i class="fas fa-search"></i> Tên sản phẩm</label>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search_term); ?>" placeholder="Nhập tên sản phẩm">
                        <small>Thử: iPhone, Samsung, MacBook</small>
                    </div>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-search"></i> Tìm Kiếm
                    </button>
                </form>

                <?php if ($sql_query): ?>
                    <div class="debug-box">
                        <h3><i class="fas fa-code"></i> Câu lệnh SQL:</h3>
                        <div class="code-example">
                            <pre><code><?php echo htmlspecialchars($sql_query); ?></code></pre>
                        </div>
                        
                        <?php if ($attack_detected): ?>
                            <div class="highlight-box danger">
                                <i class="fas fa-skull-crossbones"></i>
                                <div>
                                    <strong>Phân tích tấn công:</strong>
                                    <p style="margin-top: 10px;">Input của bạn chứa lệnh <code><?php echo htmlspecialchars($attack_type); ?></code></p>
                                    <p>Trên hệ thống SQL Server/PostgreSQL/PDO, các câu lệnh sau dấu <code>;</code> sẽ được thực thi!</p>
                                    
                                    <?php
                                    // Tách và hiển thị từng câu lệnh
                                    $statements = explode(';', $search_term);
                                    if (count($statements) > 1) {
                                        echo "<h4 style='margin-top: 15px;'>Các câu lệnh sẽ được thực thi:</h4>";
                                        echo "<ol style='margin-left: 20px; color: #fca5a5;'>";
                                        foreach ($statements as $stmt) {
                                            $stmt = trim($stmt);
                                            if (!empty($stmt) && $stmt != '--') {
                                                echo "<li style='margin: 5px 0;'><code>" . htmlspecialchars($stmt) . ";</code></li>";
                                            }
                                        }
                                        echo "</ol>";
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($result_data) && !$attack_detected): ?>
                            <h3><i class="fas fa-database"></i> Kết quả tìm kiếm (<?php echo count($result_data); ?> sản phẩm):</h3>
                            <div class="result-table">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Tên sản phẩm</th>
                                            <th>Giá</th>
                                            <th>Mô tả</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($result_data as $row): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                                <td><strong><?php echo number_format($row['price']); ?>đ</strong></td>
                                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="attack-hints">
                    <h3><i class="fas fa-bomb"></i> Payload phá hoại (THỬ THỰC SỰ!):</h3>
                    <ul style="list-style: none;">
                        <li style="margin: 10px 0;">
                            <strong>1. Xóa bảng users:</strong><br>
                            <code>'; DROP TABLE users</code>
                        </li>
                        <li style="margin: 10px 0;">
                            <strong>2. Xóa tất cả sản phẩm:</strong><br>
                            <code>'; DELETE FROM products</code>
                        </li>
                        <li style="margin: 10px 0;">
                            <strong>3. Đổi giá = 1đ:</strong><br>
                            <code>'; UPDATE products SET price = 1 WHERE id > 0</code>
                        </li>
                        <li style="margin: 10px 0;">
                            <strong>4. Tạo admin backdoor:</strong><br>
                            <code>'; INSERT INTO users (username, password_plain, role) VALUES ('hacker', '123', 'admin')</code>
                        </li>
                        <li style="margin: 10px 0;">
                            <strong>5. Leo quyền admin:</strong><br>
                            <code>'; UPDATE users SET role = 'admin' WHERE id = 2</code>
                        </li>
                    </ul>
                    
                    <div style="margin-top: 20px; padding: 15px; background: rgba(239,68,68,0.2); border: 2px solid var(--danger); border-radius: 8px;">
                        <h4 style="color: var(--danger);"><i class="fas fa-fire"></i> CẢNH BÁO:</h4>
                        <p style="color: var(--text); margin-top: 10px;">
                            Các lệnh này sẽ <strong>THỰC SỰ PHÁ HOẠI</strong> database!<br>
                            Sau khi tấn công, vào <a href="restore.php" style="color: #fcd34d; text-decoration: underline; font-weight: bold;">restore.php</a> để khôi phục!
                        </p>
                    </div>
                </div>

                <div style="margin-top: 20px; padding: 15px; background: rgba(59,130,246,0.1); border-left: 4px solid var(--info); border-radius: 8px;">
                    <h4 style="color: var(--info);"><i class="fas fa-info-circle"></i> Tại sao MySQL không bị?</h4>
                    <p style="margin-top: 10px; color: var(--text-muted);">
                        MySQL với <strong>mysqli</strong> không cho phép multiple statements mặc định để bảo vệ khỏi tấn công này.
                        Nhưng <strong>SQL Server, PostgreSQL, Oracle, và PHP PDO</strong> (nếu config sai) VẪN dễ bị tấn công!
                    </p>
                </div>
            </div>

            <div class="info-panel">
                <div class="card">
                    <h3><i class="fas fa-code"></i> Code dễ bị tấn công</h3>
                    <div class="code-example">
                        <pre><code>// ❌ SQL Server, PostgreSQL, PDO
$search = $_GET['search'];
$sql = "SELECT * FROM products 
        WHERE name LIKE '%$search%'";

// Nếu $search = "'; DROP TABLE users; --"
// → Thực thi 2 câu lệnh:
// 1. SELECT ... WHERE name LIKE '%%'
// 2. DROP TABLE users</code></pre>
                    </div>
                </div>

                <div class="card">
                    <h3><i class="fas fa-bomb"></i> Hậu quả thực tế</h3>
                    <div class="risk-grid">
                        <div class="risk-item severe">
                            <i class="fas fa-database"></i>
                            <div>
                                <h4>Mất dữ liệu vĩnh viễn</h4>
                                <p>DROP TABLE → không thể khôi phục</p>
                            </div>
                        </div>
                        <div class="risk-item high">
                            <i class="fas fa-dollar-sign"></i>
                            <div>
                                <h4>Thiệt hại tài chính</h4>
                                <p>Đổi giá, số dư, giao dịch</p>
                            </div>
                        </div>
                        <div class="risk-item high">
                            <i class="fas fa-user-secret"></i>
                            <div>
                                <h4>Backdoor</h4>
                                <p>Tạo tài khoản admin ẩn</p>
                            </div>
                        </div>
                        <div class="risk-item medium">
                            <i class="fas fa-gavel"></i>
                            <div>
                                <h4>Vi phạm pháp luật</h4>
                                <p>GDPR: phạt đến 4% doanh thu</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3><i class="fas fa-shield-alt"></i> Cách phòng chống</h3>
                    <ul class="security-list">
                        <li><i class="fas fa-check-circle"></i> <strong>Prepared Statements</strong> - Bắt buộc!</li>
                        <li><i class="fas fa-check-circle"></i> <strong>Input Validation</strong> - Whitelist characters</li>
                        <li><i class="fas fa-check-circle"></i> <strong>Escape Special Chars</strong> - Thoát ; ' " \</li>
                        <li><i class="fas fa-check-circle"></i> <strong>Database Permissions</strong> - User chỉ có quyền SELECT</li>
                        <li><i class="fas fa-check-circle"></i> <strong>WAF</strong> - Web Application Firewall</li>
                    </ul>
                    
                    <h4 style="margin-top: 20px;">✅ Code an toàn:</h4>
                    <div class="code-example">
                        <pre><code>// Prepared Statement
$stmt = $conn->prepare(
    "SELECT * FROM products 
     WHERE name LIKE ?"
);

$search_param = "%$search%";
$stmt->bind_param("s", $search_param);
$stmt->execute();

// Input KHÔNG THỂ chèn thêm câu lệnh!</code></pre>
                    </div>
                </div>

                <div class="card">
                    <h3><i class="fas fa-newspaper"></i> Vụ việc nổi tiếng</h3>
                    <div style="padding: 10px; background: rgba(239,68,68,0.1); border-radius: 8px; margin: 10px 0;">
                        <h4>Little Bobby Tables (xkcd)</h4>
                        <p style="margin-top: 5px;">Học sinh tên: <code>Robert'); DROP TABLE Students;--</code></p>
                        <p>→ Xóa toàn bộ database học sinh của trường!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
    </style>
</body>
</html>
