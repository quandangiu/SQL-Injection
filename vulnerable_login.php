<?php
// vulnerable_login.php - FORM ĐĂNG NHẬP DỄ BỊ SQL INJECTION
// ⚠️ CẢNH BÁO: Đây là ví dụ về code KHÔNG AN TOÀN - chỉ dùng để học tập!

session_start();
require_once 'config.php';

$error = '';
$success = '';
$sql_query = '';
$result_data = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // ❌ NGUY HIỂM: Nối chuỗi trực tiếp vào câu lệnh SQL
    // Đây là lỗ hổng bảo mật nghiêm trọng!
    // Sử dụng password_plain (không hash) để dễ demo SQL Injection
    $sql = "SELECT * FROM users WHERE username = '$username' AND password_plain = '$password'";
    
    // Lưu câu query để hiển thị
    $sql_query = $sql;
    
    try {
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            // Lấy tất cả kết quả
            while ($row = $result->fetch_assoc()) {
                $result_data[] = $row;
            }
            
            // Lấy user đầu tiên để đăng nhập
            $user = $result_data[0];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            $success = "✅ Đăng nhập thành công! Chào mừng " . htmlspecialchars($user['full_name']);
        } else {
            $error = "❌ Sai tên đăng nhập hoặc mật khẩu!";
        }
    } catch (Exception $e) {
        // ❌ NGUY HIỂM: Hiển thị lỗi SQL chi tiết
        $error = "❌ Lỗi SQL: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vulnerable Login - SQL Injection Demo</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <i class="fas fa-bug"></i>
                <h1>Vulnerable Login</h1>
            </div>
            <a href="index.html" class="btn-back"><i class="fas fa-arrow-left"></i> Quay lại</a>
        </header>

        <div class="login-container">
            <div class="login-card danger-card">
                <h2><i class="fas fa-exclamation-triangle"></i> Form Không Bảo Mật</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="login-form">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Tên đăng nhập</label>
                        <input type="text" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" placeholder="Nhập username" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Mật khẩu</label>
                        <input type="password" name="password" placeholder="Nhập password">
                    </div>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-sign-in-alt"></i> Đăng Nhập
                    </button>
                </form>

                <?php if ($sql_query): ?>
                    <div class="debug-box">
                        <h3><i class="fas fa-code"></i> Câu lệnh SQL thực tế:</h3>
                        <div class="code-example">
                            <pre><code><?php echo htmlspecialchars($sql_query); ?></code></pre>
                        </div>
                        
                        <?php if (!empty($result_data)): ?>
                            <h3><i class="fas fa-database"></i> Dữ liệu trả về (<?php echo count($result_data); ?> bản ghi):</h3>
                            <div class="result-table">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Full Name</th>
                                            <th>Role</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($result_data as $row): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                                <td><span class="badge badge-<?php echo $row['role'] == 'admin' ? 'danger' : 'info'; ?>"><?php echo htmlspecialchars($row['role']); ?></span></td>
                                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="attack-hints">
                    <h3><i class="fas fa-lightbulb"></i> Gợi ý tấn công:</h3>
                    <ul>
                        <li><code>admin' OR '1'='1' --</code> (bypass login)</li>
                        <li><code>' OR 1=1 --</code> (lấy tất cả users)</li>
                        <li><code>admin' --</code> (đăng nhập với username cụ thể)</li>
                        <li><code>admin' UNION SELECT 1,2,3,4,5,6,7 --</code> (union attack)</li>
                    </ul>
                </div>
            </div>

            <div class="info-panel">
                <div class="card">
                    <h3><i class="fas fa-code"></i> Mã nguồn dễ bị tấn công</h3>
                    <div class="code-example">
                        <pre><code>&lt;?php
$username = $_POST['username'];
$password = $_POST['password'];

// ❌ NGUY HIỂM: Nối chuỗi trực tiếp
$sql = "SELECT * FROM users 
        WHERE username = '$username' 
        AND password = '$password'";

$result = $conn->query($sql);
?&gt;</code></pre>
                    </div>
                </div>

                <div class="card">
                    <h3><i class="fas fa-skull-crossbones"></i> Vấn đề</h3>
                    <ul class="problem-list">
                        <li><i class="fas fa-times-circle"></i> Không sử dụng Prepared Statements</li>
                        <li><i class="fas fa-times-circle"></i> Không validate/sanitize input</li>
                        <li><i class="fas fa-times-circle"></i> Không hash password</li>
                        <li><i class="fas fa-times-circle"></i> Hiển thị lỗi SQL chi tiết</li>
                        <li><i class="fas fa-times-circle"></i> Cho phép ký tự đặc biệt SQL</li>
                    </ul>
                </div>

                <div class="card">
                    <h3><i class="fas fa-graduation-cap"></i> Bài học</h3>
                    <div class="highlight-box warning">
                        <p><strong>KHÔNG BAO GIỜ</strong> nối trực tiếp user input vào câu lệnh SQL!</p>
                        <p>Kẻ tấn công có thể chèn bất kỳ lệnh SQL nào vào input.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
