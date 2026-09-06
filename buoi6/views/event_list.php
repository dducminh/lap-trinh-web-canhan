<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Buổi 6 - Mô hình MVC & Repository</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #eee; }
        .form-row { display: flex; gap: 10px; margin-bottom: 10px; }
        input { flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        button, a.btn { padding: 8px 12px; border: none; border-radius: 4px; color: #fff; background: #007bff; text-decoration: none; cursor: pointer; }
        a.btn-danger { background: #dc3545; }
        a.btn-warning { background: #ffc107; color: #000; }
        .pagination a { padding: 5px 10px; border: 1px solid #ccc; text-decoration: none; margin-right: 4px; }
        .pagination a.active { background: #007bff; color: #fff; }
    </style>
</head>
<body>
<div class="container">
    <h2>Quản Lý Sự Kiện (Kiến trúc MVC & Repository Pattern)</h2>
    <?php if ($message): ?><p style="color: green;"><?= htmlspecialchars($message) ?></p><?php endif; ?>

    <form method="POST" action="index.php">
        <?php if ($editEvent): ?><input type="hidden" name="id" value="<?= $editEvent['id'] ?>"><?php endif; ?>
        <div class="form-row">
            <input type="text" name="title" placeholder="Tên sự kiện" value="<?= htmlspecialchars($editEvent['title'] ?? '') ?>" required>
            <input type="text" name="club_name" placeholder="Tên CLB" value="<?= htmlspecialchars($editEvent['club_name'] ?? '') ?>" required>
        </div>
        <div class="form-row">
            <input type="date" name="event_date" value="<?= htmlspecialchars($editEvent['event_date'] ?? '') ?>" required>
            <input type="number" name="max_participants" min="1" placeholder="Giới hạn người" value="<?= htmlspecialchars($editEvent['max_participants'] ?? '') ?>" required>
            <input type="number" name="registered_count" min="0" placeholder="Đã đăng ký" value="<?= htmlspecialchars($editEvent['registered_count'] ?? '0') ?>" required>
        </div>
        <button type="submit"><?= $editEvent ? 'Lưu cập nhật' : 'Thêm mới' ?></button>
        <?php if ($editEvent): ?><a href="index.php" class="btn" style="background:#6c757d">Hủy</a><?php endif; ?>
    </form>

    <form method="GET" action="index.php" style="margin-top: 20px;">
        <input type="text" name="search" placeholder="Tìm kiếm..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Tìm</button>
    </form>

    <table>
        <thead>
            <tr><th>ID</th><th>Tên sự kiện</th><th>CLB</th><th>Ngày</th><th>Số lượng</th><th>Thao tác</th></tr>
        </thead>
        <tbody>
            <?php foreach ($events as $row): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['club_name']) ?></td>
                <td><?= $row['event_date'] ?></td>
                <td><?= $row['registered_count'] ?> / <?= $row['max_participants'] ?></td>
                <td>
                    <a href="index.php?action=edit&id=<?= $row['id'] ?>" class="btn btn-warning">Sửa</a>
                    <a href="index.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Chắc chắn xóa?');">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="pagination" style="margin-top: 15px;">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="index.php?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</div>
</body>
</html>