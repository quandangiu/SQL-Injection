<?php
/**
 * SETUP PASSWORD - Cập nhật password hash vào database
 * Chạy file này SAU KHI import database.sql
 */

require_once 'config.php';

// Password muốn set
$password = "123456";

// Tạo hash
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<!DOCTYPE html>
<html lang='vi'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Setup Password</title>
    <link rel='stylesheet' href='style.css'>
</head>
<body>
    <div class='container'>
        <div class='card' style='max-width:800px; margin:50px auto;'>
            <h2>🔐 Setup Password Hash</h2>";

// Test kết nối
if ($conn->connect_error) {
    echo "<div class='alert alert-danger'>❌ Không thể kết nối database!</div>";
    echo "<p>Lỗi: " . $conn->connect_error . "</p>";
    echo "<p><strong>Hướng dẫn:</strong></p>";
    echo "<ol>
            <li>Đảm bảo MySQL đang chạy</li>
            <li>Import file database.sql vào phpMyAdmin trước</li>
            <li>Kiểm tra thông tin trong config.php</li>
          </ol>";
} else {
    echo "<div class='alert alert-success'>✅ Kết nối database thành công!</div>";
    
    echo "<h3>Thông tin:</h3>";
    echo "<ul>
            <li><strong>Password:</strong> $password</li>
            <li><strong>Hash:</strong> <code style='word-break:break-all;'>$hash</code></li>
          </ul>";
    
    // Update password cho tất cả users
    $sql = "UPDATE users SET password = ?, password_plain = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("ss", $hash, $password);
        
        if ($stmt->execute()) {
            $affected = $stmt->affected_rows;
            echo "<div class='alert alert-success'>
                    ✅ Cập nhật thành công! 
                    <br>Số bản ghi được cập nhật: <strong>$affected</strong>
                  </div>";
            
            echo "<h3>Tài khoản có thể đăng nhập:</h3>";
            
            // Lấy danh sách users
            $result = $conn->query("SELECT username, email, role FROM users");
            if ($result && $result->num_rows > 0) {
                echo "<table style='width:100%; margin-top:20px;'>
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Email</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>";
                
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td><strong>" . htmlspecialchars($row['username']) . "</strong></td>
                            <td><code>123456</code></td>
                            <td>" . htmlspecialchars($row['email']) . "</td>
                            <td><span class='badge badge-" . ($row['role'] == 'admin' ? 'danger' : 'info') . "'>" . htmlspecialchars($row['role']) . "</span></td>
                          </tr>";
                }
                
                echo "</tbody></table>";
            }
            
            echo "<div style='margin-top:30px; padding:20px; background:rgba(16,185,129,0.1); border-left:4px solid #10b981; border-radius:8px;'>
                    <h3 style='color:#6ee7b7;'>🎉 Hoàn tất!</h3>
                    <p>Bây giờ bạn có thể đăng nhập vào:</p>
                    <ul>
                        <li><a href='vulnerable_login.php' style='color:#6ee7b7;'>Form Vulnerable (Không an toàn)</a></li>
                        <li><a href='secure_login.php' style='color:#6ee7b7;'>Form Secure (An toàn)</a></li>
                    </ul>
                    <p><strong>Tất cả tài khoản đều dùng password:</strong> <code>123456</code></p>
                  </div>";
            
            echo "<div style='margin-top:20px;'>
                    <a href='index.html' class='btn btn-success'>← Quay lại trang chủ</a>
                  </div>";
            
        } else {
            echo "<div class='alert alert-danger'>❌ Lỗi khi cập nhật: " . $stmt->error . "</div>";
        }
        
        $stmt->close();
    } else {
        echo "<div class='alert alert-danger'>❌ Không thể chuẩn bị câu lệnh SQL</div>";
    }
}

$conn->close();

echo "        </div>
    </div>
</body>
</html>";
?>
