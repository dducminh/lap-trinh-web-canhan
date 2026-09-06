<?php
session_start();
$host = 'localhost';
$dbname = 'web_canhan';
$username = 'root';
$password = '';
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

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
        body { font-family: Arial; background: #eef2f5; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .box { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 320px; }
        input { width: 100%; padding: 8px; margin: 8px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
<div class="box">
    <h3>Đăng Nhập Hệ Thống</h3>
    <?php if ($error): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>
    <form method="POST">
        <label>Tài khoản:</label>
        <input type="text" name="username" placeholder="admin hoặc student" required>
        <label>Mật khẩu:</label>
        <input type="password" name="password" placeholder="123456" required>
        <button type="submit">Đăng nhập</button>
    </form>
</div>
</body>
</html>