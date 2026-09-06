<?php
session_start();

// SERVER GUARD: Chặn truy cập trái phép
if (!isset($_SESSION['user'])) {
    header("Location: login.php?error=unauthorized");
    exit;
}

$currentUser = $_SESSION['user'];
$isAdmin = ($currentUser['role'] === 'admin');

// Kết nối CSDL
$host = 'localhost';
$dbname = 'web_canhan';
$username = 'root';
$password = '123456';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Xử lý Thêm sự kiện (Chỉ Admin)
    $msg = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
        if (!$isAdmin) {
            die("Lỗi 403: Bạn không có quyền thực hiện hành động này!");
        }
        $title = trim($_POST['title'] ?? '');
        $club_name = trim($_POST['club_name'] ?? '');
        $event_date = $_POST['event_date'] ?? '';
        $max_participants = (int)($_POST['max_participants'] ?? 0);

        if ($title && $club_name && $event_date && $max_participants > 0) {
            $stmt = $pdo->prepare("INSERT INTO events (title, club_name, event_date, max_participants, registered_count) VALUES (?, ?, ?, ?, 0)");
            $stmt->execute([$title, $club_name, $event_date, $max_participants]);
            $msg = "Admin đã thêm sự kiện mới thành công!";
        }
    }

    // Xử lý Xóa sự kiện (Chỉ Admin)
    if (isset($_GET['delete_id'])) {
        if (!$isAdmin) {
            die("Lỗi 403: Bạn không có quyền xóa!");
        }
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([(int)$_GET['delete_id']]);
        header("Location: dashboard.php");
        exit;
    }

    // Lấy danh sách sự kiện
    $events = $pdo->query("SELECT * FROM events ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ Thống Phân Quyền - Buổi 7</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 25px; }
        .container { max-width: 1000px; margin: auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .header-bar { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
        .badge-admin { background: #dc3545; color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 13px; }
        .badge-student { background: #28a745; color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 13px; }
        .role-notice { padding: 12px; border-radius: 6px; margin-bottom: 20px; }
        .role-admin-box { background: #fff3cd; border-left: 5px solid #ffc107; color: #856404; }
        .role-student-box { background: #d1ecf1; border-left: 5px solid #17a2b8; color: #0c5460; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #dee2e6; padding: 10px; text-align: left; }
        th { background: #f8f9fa; }
        .form-row { display: flex; gap: 10px; margin-bottom: 10px; }
        input { flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        button, a.btn { padding: 8px 14px; border: none; border-radius: 4px; color: #fff; background: #007bff; text-decoration: none; cursor: pointer; display: inline-block; }
        .btn-danger { background: #dc3545; font-size: 13px; padding: 5px 10px; }
        .btn-secondary { background: #6c757d; }
    </style>
</head>
<body>
<div class="container">
    <div class="header-bar">
        <div>
            <h2 style="margin: 0;">Bảng Điều Khiển Sự Kiện</h2>
            <span>Người dùng: <strong><?= htmlspecialchars($currentUser['username']) ?></strong> | Vai trò: 
                <span class="<?= $isAdmin ? 'badge-admin' : 'badge-student' ?>">
                    <?= strtoupper(htmlspecialchars($currentUser['role'])) ?>
                </span>
            </span>
        </div>
        <div>
            <a href="logout.php" class="btn btn-secondary">Đăng xuất</a>
        </div>
    </div>

    <!-- Phân định quyền hạn hiển thị theo Role -->
    <?php if ($isAdmin): ?>
        <div class="role-notice role-admin-box">
            🛡️ <strong>Khu vực Quản trị viên (Admin Route):</strong> Bạn có toàn quyền quản trị, thêm mới và xóa bỏ sự kiện trong hệ thống.
        </div>
        
        <?php if ($msg): ?><p style="color: green; font-weight: bold;"><?= $msg ?></p><?php endif; ?>

        <!-- Form chỉ Admin mới nhìn thấy và dùng được -->
        <form method="POST" action="dashboard.php" style="background: #fafafa; padding: 15px; border: 1px dashed #bbb; border-radius: 6px; margin-bottom: 20px;">
            <input type="hidden" name="action" value="create">
            <h4 style="margin-top: 0;">+ Thêm sự kiện mới (Quyền Admin)</h4>
            <div class="form-row">
                <input type="text" name="title" placeholder="Tên sự kiện..." required>
                <input type="text" name="club_name" placeholder="CLB tổ chức..." required>
            </div>
            <div class="form-row">
                <input type="date" name="event_date" required>
                <input type="number" name="max_participants" min="1" placeholder="Giới hạn người" required>
            </div>
            <button type="submit">Lưu sự kiện</button>
        </form>
    <?php else: ?>
        <div class="role-notice role-student-box">
            🎓 <strong>Khu vực Sinh viên (Student Route):</strong> Bạn có quyền xem lịch sự kiện và trạng thái đăng ký. Các thao tác quản trị viên đã bị ẩn và chặn ở tầng Server.
        </div>
    <?php endif; ?>

    <!-- Bảng danh sách sự kiện -->
    <h3>Danh Sách Sự Kiện Đang Mở</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên Sự Kiện</th>
                <th>CLB</th>
                <th>Ngày Tổ Chức</th>
                <th>Số Lượng</th>
                <?php if ($isAdmin): ?><th>Thao tác Admin</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $e): ?>
            <tr>
                <td><?= $e['id'] ?></td>
                <td><strong><?= htmlspecialchars($e['title']) ?></strong></td>
                <td><?= htmlspecialchars($e['club_name']) ?></td>
                <td><?= $e['event_date'] ?></td>
                <td><?= $e['registered_count'] ?> / <?= $e['max_participants'] ?></td>
                <?php if ($isAdmin): ?>
                <td>
                    <a href="dashboard.php?delete_id=<?= $e['id'] ?>" class="btn btn-danger" onclick="return confirm('Chắc chắn muốn xóa sự kiện này?');">Xóa</a>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>