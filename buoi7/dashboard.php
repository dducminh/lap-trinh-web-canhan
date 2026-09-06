<?php
session_start();

// SERVER GUARD: Kiểm tra đăng nhập
if (!isset($_SESSION['user'])) {
    header("Location: login.php?error=unauthorized");
    exit;
}

$currentUser = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Buổi 7</title>
</head>
<body style="font-family: Arial; padding: 20px;">
    <h2>Xin chào: <?= htmlspecialchars($currentUser['username']) ?> (Vai trò: <?= htmlspecialchars($currentUser['role']) ?>)</h2>
    <p>Trạng thái: Đã đăng nhập và vượt qua Server Guard.</p>

    <?php if ($currentUser['role'] === 'admin'): ?>
        <div style="background: #e2f0cb; padding: 15px; border-radius: 5px;">
            <strong>Khu vực Quản trị viên (Admin Route):</strong> Bạn có toàn quyền thêm, sửa, xóa sự kiện.
        </div>
    <?php else: ?>
        <div style="background: #b5ead7; padding: 15px; border-radius: 5px;">
            <strong>Khu vực Sinh viên (Student Route):</strong> Bạn chỉ có quyền xem và đăng ký sự kiện.
        </div>
    <?php endif; ?>

    <p style="margin-top: 20px;"><a href="logout.php">Đăng xuất</a></p>
</body>
</html>