<?php
session_start();

$host = 'localhost';
$dbname = 'web_canhan';
$username = 'root';
$password = '123456';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Tự động đồng bộ chuẩn mật khẩu 123456 cho tài khoản admin và student
    $passHash = password_hash('123456', PASSWORD_DEFAULT);
    $pdo->prepare("UPDATE users SET password = ? WHERE username IN ('admin', 'student')")->execute([$passHash]);
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$user]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($account && password_verify($pass, $account['password'])) {
        $_SESSION['user'] = [
            'id' => $account['id'],
            'username' => $account['username'],
            'role' => $account['role']
        ];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Tài khoản hoặc mật khẩu không chính xác!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Nhập - Buổi 7</title>
    <style>
        body { font-family: Arial, sans-serif; background: #eef2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 340px; }
        input { width: 100%; padding: 10px; margin: 8px 0 15px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background: #0056b3; }
        .error { color: #dc3545; margin-bottom: 10px; font-size: 14px; }
    </style>
</head>
<body>
<div class="box">
    <h3 style="margin-top: 0; text-align: center;">Đăng Nhập Hệ Thống</h3>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST" action="login.php">
        <label>Tài khoản:</label>
        <input type="text" name="username" placeholder="admin hoặc student" required>
        <label>Mật khẩu:</label>
        <input type="password" name="password" placeholder="123456" required>
        <button type="submit">Đăng nhập</button>
    </form>
</div>
</body>
</html>