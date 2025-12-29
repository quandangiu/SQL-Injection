<?php
// secure_login.php - FORM ĐĂNG NHẬP AN TOÀN
// ✅ Sử dụng Prepared Statements để phòng chống SQL Injection

session_start();
require_once 'config.php';

$error = '';
$success = '';
$sql_query = '';
$attempts = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // ✅ BƯỚC 1: Validation đầu vào
    $validation_errors = [];
    
    // Kiểm tra độ dài username
    if (strlen($username) < 3 || strlen($username) > 50) {
        $validation_errors[] = "Username phải từ 3-50 ký tự";
    }
    
    // Kiểm tra ký tự hợp lệ trong username
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $validation_errors[] = "Username chỉ được chứa chữ, số và dấu gạch dưới";
    }
    
    // Kiểm tra password không rỗng
    if (empty($password)) {
        $validation_errors[] = "Password không được để trống";
    }
    
    if (!empty($validation_errors)) {
        $error = "❌ " . implode("<br>", $validation_errors);
    } else {
        // ✅ BƯỚC 2: Sử dụng Prepared Statements
        // Tham số được tách biệt hoàn toàn khỏi câu lệnh SQL
        $sql = "SELECT id, username, password, email, full_name, role FROM users WHERE username = ?";
        
        // Lưu câu query để hiển thị (với placeholder)
        $sql_query = $sql . " [Tham số: username = '" . htmlspecialchars($username) . "']";
        
        try {
            // Chuẩn bị câu lệnh
            $stmt = $conn->prepare($sql);
            
            if ($stmt === false) {
                throw new Exception("Không thể chuẩn bị câu lệnh SQL");
            }
            
            // Bind tham số (s = string)
            $stmt->bind_param("s", $username);
            
            // Thực thi
            $stmt->execute();
            
            // Lấy kết quả
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                
                // ✅ BƯỚC 3: Verify password đã hash
                // Password trong DB đã được hash bằng password_hash()
                // Chúng ta dùng password_verify() để kiểm tra
                if (password_verify($password, $user['password'])) {
                    // Đăng nhập thành công
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    
                    $success = "✅ Đăng nhập thành công! Chào mừng " . htmlspecialchars($user['full_name']);
                    
                    // Hiển thị thông tin user (không bao gồm password)
                    unset($user['password']);
                    $user_info = $user;
                } else {
                    $error = "❌ Sai tên đăng nhập hoặc mật khẩu!";
                    $attempts++;
                }
            } else {
                $error = "❌ Sai tên đăng nhập hoặc mật khẩu!";
                $attempts++;
            }
            
            $stmt->close();
            
        } catch (Exception $e) {
            // ✅ BƯỚC 4: Không hiển thị lỗi chi tiết cho user
            $error = "❌ Có lỗi xảy ra. Vui lòng thử lại sau.";
            // Log lỗi vào file thay vì hiển thị
            error_log("Login error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Login - SQL Injection Demo</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <i class="fas fa-shield-alt"></i>
                <h1>Secure Login</h1>
            </div>
            <a href="index.html" class="btn-back"><i class="fas fa-arrow-left"></i> Quay lại</a>
        </header>

        <div class="login-container">
            <div class="login-card success-card">
                <h2><i class="fas fa-lock"></i> Form Bảo Mật</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                        <?php if ($attempts > 0): ?>
                            <p><small>Số lần thử sai: <?php echo $attempts; ?></small></p>
                        <?php endif; ?>
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
                        <input type="text" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" placeholder="Nhập username (a-z, 0-9, _)" required minlength="3" maxlength="50" pattern="[a-zA-Z0-9_]+">
                        <small>Chỉ chữ, số và dấu gạch dưới</small>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Mật khẩu</label>
                        <input type="password" name="password" placeholder="Nhập password" required>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-shield-alt"></i> Đăng Nhập An Toàn
                    </button>
                </form>

                <?php if ($sql_query): ?>
                    <div class="debug-box success">
                        <h3><i class="fas fa-code"></i> Câu lệnh SQL Prepared Statement:</h3>
                        <div class="code-example">
                            <pre><code><?php echo htmlspecialchars($sql_query); ?></code></pre>
                        </div>
                        <div class="highlight-box success">
                            <i class="fas fa-check-circle"></i>
                            <p><strong>An toàn:</strong> Tham số được tách biệt, không thể inject code!</p>
                        </div>
                        
                        <?php if (isset($user_info)): ?>
                            <h3><i class="fas fa-user-check"></i> Thông tin người dùng:</h3>
                            <div class="result-table">
                                <table>
                                    <tbody>
                                        <tr>
                                            <th>ID</th>
                                            <td><?php echo htmlspecialchars($user_info['id']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Username</th>
                                            <td><?php echo htmlspecialchars($user_info['username']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td><?php echo htmlspecialchars($user_info['email']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Họ tên</th>
                                            <td><?php echo htmlspecialchars($user_info['full_name']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Vai trò</th>
                                            <td><span class="badge badge-<?php echo $user_info['role'] == 'admin' ? 'danger' : 'info'; ?>"><?php echo htmlspecialchars($user_info['role']); ?></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="test-box">
                    <h3><i class="fas fa-vial"></i> Thử tấn công xem sao:</h3>
                    <p>Hãy thử các payload SQLi:</p>
                    <ul>
                        <li><code>admin' OR '1'='1' --</code></li>
                        <li><code>' OR 1=1 --</code></li>
                        <li><code>admin' UNION SELECT...</code></li>
                    </ul>
                    <p><strong>Kết quả:</strong> Tất cả đều thất bại! 🛡️</p>
                </div>
            </div>

            <div class="info-panel">
                <div class="card">
                    <h3><i class="fas fa-code"></i> Mã nguồn an toàn</h3>
                    <div class="code-example">
                        <pre><code>&lt;?php
// ✅ BƯỚC 1: Validation
if (strlen($username) < 3 || strlen($username) > 50) {
    die("Username không hợp lệ");
}

// ✅ BƯỚC 2: Prepared Statement
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);

// ✅ BƯỚC 3: Bind tham số
$stmt->bind_param("s", $username);
$stmt->execute();

// ✅ BƯỚC 4: Verify password
$user = $stmt->get_result()->fetch_assoc();
if (password_verify($password, $user['password'])) {
    // Đăng nhập thành công
}
?&gt;</code></pre>
                    </div>
                </div>

                <div class="card">
                    <h3><i class="fas fa-shield-alt"></i> Các biện pháp bảo mật</h3>
                    <ul class="security-list">
                        <li><i class="fas fa-check-circle"></i> <strong>Prepared Statements:</strong> Tách biệt SQL và dữ liệu</li>
                        <li><i class="fas fa-check-circle"></i> <strong>Input Validation:</strong> Kiểm tra định dạng, độ dài</li>
                        <li><i class="fas fa-check-circle"></i> <strong>Password Hashing:</strong> Sử dụng password_hash() và password_verify()</li>
                        <li><i class="fas fa-check-circle"></i> <strong>Error Handling:</strong> Không hiển thị lỗi SQL chi tiết</li>
                        <li><i class="fas fa-check-circle"></i> <strong>Escaping Output:</strong> Sử dụng htmlspecialchars()</li>
                    </ul>
                </div>

                <div class="card">
                    <h3><i class="fas fa-lightbulb"></i> Tại sao an toàn?</h3>
                    <div class="comparison-box">
                        <div class="comparison-item danger">
                            <h4>❌ Không an toàn:</h4>
                            <code>"SELECT * FROM users WHERE username = '$username'"</code>
                            <p>→ Input trộn lẫn với SQL code</p>
                        </div>
                        <div class="comparison-item success">
                            <h4>✅ An toàn:</h4>
                            <code>"SELECT * FROM users WHERE username = ?"</code>
                            <p>→ Input được xử lý như dữ liệu thuần túy</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3><i class="fas fa-info-circle"></i> Lưu ý quan trọng</h3>
                    <div class="highlight-box info">
                        <ul>
                            <li>Prepared Statements tự động escape ký tự đặc biệt</li>
                            <li>Input được xử lý như dữ liệu, KHÔNG phải SQL code</li>
                            <li>Kể cả kẻ tấn công nhập <code>' OR '1'='1</code>, nó chỉ được tìm kiếm như một chuỗi thông thường</li>
                            <li>Database driver tự động xử lý, không cần escape thủ công</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
