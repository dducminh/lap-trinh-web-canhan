<?php
// 1. KẾT NỐI DATABASE BẰNG PDO
$host = '127.0.0.1'; // hoặc 'localhost'
$dbname = 'web_canhan';
$username = 'root';
$password = '123456';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}

$thongBao = '';
$editEvent = null;

// 2. XỬ LÝ XÓA SỰ KIỆN (DELETE)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    header("Location: index.php?msg=deleted");
    exit;
}

// 3. LẤY DỮ LIỆU CẦN SỬA (GET EDIT)
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    $editEvent = $stmt->fetch(PDO::FETCH_ASSOC);
}

// 4. XỬ LÝ THÊM MỚI HOẶC CẬP NHẬT (CREATE / UPDATE - PREPARED STATEMENT)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $club_name = trim($_POST['club_name'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $max_participants = (int)($_POST['max_participants'] ?? 0);
    $registered_count = (int)($_POST['registered_count'] ?? 0);
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;

    if (!empty($title) && !empty($club_name) && !empty($event_date) && $max_participants > 0 && $registered_count >= 0) {
        if ($id) {
            // Update
            $sql = "UPDATE events SET title = ?, club_name = ?, event_date = ?, max_participants = ?, registered_count = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $club_name, $event_date, $max_participants, $registered_count, $id]);
            $thongBao = "Cập nhật sự kiện thành công!";
            $editEvent = null;
        } else {
            // Create
            $sql = "INSERT INTO events (title, club_name, event_date, max_participants, registered_count) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $club_name, $event_date, $max_participants, $registered_count]);
            $thongBao = "Thêm sự kiện thành công!";
        }
    } else {
        $thongBao = "Lỗi: Dữ liệu nhập không hợp lệ hoặc thiếu trường bắt buộc!";
    }
}

// 5. TÌM KIẾM VÀ PHÂN TRANG (READ)
$search = trim($_GET['search'] ?? '');
$limit = 4; // Số bản ghi mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$offset = ($page - 1) * $limit;

if (!empty($search)) {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM events WHERE title LIKE ? OR club_name LIKE ?");
    $countStmt->execute(["%$search%", "%$search%"]);
    $totalEvents = $countStmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM events WHERE title LIKE ? OR club_name LIKE ? ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(2, "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(3, $limit, PDO::PARAM_INT);
    $stmt->bindValue(4, $offset, PDO::PARAM_INT);
    $stmt->execute();
} else {
    $totalEvents = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
    $stmt = $pdo->prepare("SELECT * FROM events ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
}

$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalPages = ceil($totalEvents / $limit);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Buổi 5 - CRUD PDO Sự Kiện CLB</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 950px; margin: auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .form-row { display: flex; gap: 10px; margin-bottom: 12px; }
        .form-group { flex: 1; }
        label { display: block; margin-bottom: 4px; font-weight: 600; font-size: 14px; }
        input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button, .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; color: #fff; display: inline-block; font-size: 14px; }
        .btn-primary { background: #007bff; }
        .btn-warning { background: #ffc107; color: #000; }
        .btn-danger { background: #dc3545; }
        .btn-secondary { background: #6c757d; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #dee2e6; padding: 10px; text-align: left; }
        th { background: #f8f9fa; }
        .search-box { display: flex; gap: 10px; margin: 20px 0 10px; }
        .pagination { margin-top: 15px; display: flex; gap: 5px; }
        .pagination a { padding: 6px 12px; border: 1px solid #ccc; text-decoration: none; border-radius: 4px; color: #007bff; }
        .pagination a.active { background: #007bff; color: #fff; border-color: #007bff; }
        .alert { padding: 10px; background: #d4edda; color: #155724; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Quản Lý Sự Kiện CLB Sinh Viên (Buổi 5 - PDO CRUD)</h2>

    <?php if ($thongBao): ?>
        <div class="alert"><?= htmlspecialchars($thongBao) ?></div>
    <?php endif; ?>

    <!-- FORM THÊM / SỬA -->
    <h3><?= $editEvent ? 'Cập Nhật Sự Kiện' : 'Thêm Sự Kiện Mới' ?></h3>
    <form method="POST" action="index.php">
        <?php if ($editEvent): ?>
            <input type="hidden" name="id" value="<?= $editEvent['id'] ?>">
        <?php endif; ?>
        <div class="form-row">
            <div class="form-group">
                <label>Tên sự kiện:</label>
                <input type="text" name="title" value="<?= htmlspecialchars($editEvent['title'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>CLB tổ chức:</label>
                <input type="text" name="club_name" value="<?= htmlspecialchars($editEvent['club_name'] ?? '') ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Ngày tổ chức:</label>
                <input type="date" name="event_date" value="<?= htmlspecialchars($editEvent['event_date'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Giới hạn người:</label>
                <input type="number" name="max_participants" min="1" value="<?= htmlspecialchars($editEvent['max_participants'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Đã đăng ký:</label>
                <input type="number" name="registered_count" min="0" value="<?= htmlspecialchars($editEvent['registered_count'] ?? '0') ?>" required>
            </div>
        </div>
        <button type="submit" class="btn-primary"><?= $editEvent ? 'Lưu thay đổi' : 'Thêm sự kiện' ?></button>
        <?php if ($editEvent): ?>
            <a href="index.php" class="btn btn-secondary">Hủy bỏ</a>
        <?php endif; ?>
    </form>

    <hr style="margin-top: 25px;">

    <!-- TÌM KIẾM -->
    <form method="GET" action="index.php" class="search-box">
        <input type="text" name="search" placeholder="Tìm theo tên sự kiện hoặc CLB..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn-primary">Tìm kiếm</button>
        <?php if ($search): ?>
            <a href="index.php" class="btn btn-secondary">Đặt lại</a>
        <?php endif; ?>
    </form>

    <!-- BẢNG HIỂN THỊ -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên Sự Kiện</th>
                <th>CLB</th>
                <th>Ngày Tổ Chức</th>
                <th>Đã ĐK / Tối Đa</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($events) > 0): ?>
                <?php foreach ($events as $row): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><strong><?= htmlspecialchars($row['title']) ?></strong></td>
                    <td><?= htmlspecialchars($row['club_name']) ?></td>
                    <td><?= htmlspecialchars($row['event_date']) ?></td>
                    <td><?= $row['registered_count'] ?> / <?= $row['max_participants'] ?></td>
                    <td>
                        <a href="index.php?action=edit&id=<?= $row['id'] ?>" class="btn btn-warning" style="padding: 4px 8px;">Sửa</a>
                        <a href="index.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-danger" style="padding: 4px 8px;" onclick="return confirm('Bạn có chắc muốn xóa?');">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align: center;">Không tìm thấy dữ liệu.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- PHÂN TRANG -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="index.php?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>
</body>
</html>