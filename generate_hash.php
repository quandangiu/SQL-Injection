<?php
/**
 * GENERATE PASSWORD HASH
 * File này dùng để tạo password hash cho demo
 */

$password = "123456";
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>Password Hash Generator</h2>";
echo "<p><strong>Password:</strong> $password</p>";
echo "<p><strong>Hash:</strong> $hash</p>";
echo "<hr>";
echo "<h3>Copy hash này vào database.sql:</h3>";
echo "<textarea style='width:100%; height:100px; font-family:monospace;'>$hash</textarea>";

// Test verify
if (password_verify($password, $hash)) {
    echo "<p style='color:green;'>✅ Verify thành công!</p>";
} else {
    echo "<p style='color:red;'>❌ Verify thất bại!</p>";
}
?>
