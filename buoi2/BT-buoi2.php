<?php
// 1. Hàm tự định nghĩa xử lý nghiệp vụ: Đánh giá trạng thái và mức độ đăng ký sự kiện
function danhGiaTrangThaiSuKien($soLuongDangKy, $gioiHan) {
    if ($gioiHan <= 0) {
        return ['status' => 'Không hợp lệ', 'class' => 'danger'];
    }
    
    $tyLe = ($soLuongDangKy / $gioiHan) * 100;

    if ($soLuongDangKy >= $gioiHan) {
        return ['status' => 'Đã hết chỗ (100%)', 'class' => 'danger'];
    } elseif ($tyLe >= 80) {
        return ['status' => 'Sắp hết chỗ (' . round($tyLe, 1) . '%)', 'class' => 'warning'];
    } else {
        return ['status' => 'Còn chỗ (' . round($tyLe, 1) . '%)', 'class' => 'success'];
    }
}

// 2. Dữ liệu mẫu ban đầu tổ chức bằng mảng
$danhSachSuKien = [
    [
        'ten' => 'Chào tân sinh viên 2026',
        'clb' => 'CLB Tình nguyện',
        'ngay' => '2026-09-15',
        'gioiHan' => 200,
        'daDangKy' => 195
    ],
    [
        'ten' => 'Workshop Git & GitHub Cơ bản',
        'clb' => 'CLB Tin học',
        'ngay' => '2026-09-20',
        'gioiHan' => 80,
        'daDangKy' => 45
    ]
];

// 3. Tiếp nhận và xử lý dữ liệu gửi từ Form (POST)
$thongBao = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenSuKien = trim($_POST['ten_su_kien'] ?? '');
    $tenCLB = trim($_POST['ten_clb'] ?? '');
    $ngayToChuc = $_POST['ngay_to_chuc'] ?? '';
    $gioiHanNguoi = (int)($_POST['gioi_han_nguoi'] ?? 0);
    $soDaDangKy = (int)($_POST['da_dang_ky'] ?? 0);

    // Kiểm tra điều kiện nhập liệu hợp lệ
    if (!empty($tenSuKien) && !empty($tenCLB) && !empty($ngayToChuc) && $gioiHanNguoi > 0) {
        // Thêm đối tượng mới vào mảng dữ liệu
        $danhSachSuKien[] = [
            'ten' => $tenSuKien,
            'clb' => $tenCLB,
            'ngay' => $ngayToChuc,
            'gioiHan' => $gioiHanNguoi,
            'daDangKy' => $soDaDangKy
        ];
        $thongBao = "Thêm sự kiện thành công!";
    } else {
        $thongBao = "Vui lòng nhập đầy đủ và hợp lệ các trường thông tin!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Buổi 2 - Quản Lý Sự Kiện CLB</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f4f6f9; margin: 0; padding: 30px; }
        .container { max-width: 900px; margin: 0 auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.08); }
        h1, h2 { color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; }
        input, select { width: 100%; padding: 9px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn-submit { background-color: #007bff; color: white; border: none; padding: 10px 20px; font-size: 15px; border-radius: 4px; cursor: pointer; }
        .btn-submit:hover { background-color: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #dee2e6; padding: 10px; text-align: left; }
        th { background-color: #f1f3f5; color: #495057; }
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 13px; font-weight: 600; }
        .success { background-color: #d4edda; color: #155724; }
        .warning { background-color: #fff3cd; color: #856404; }
        .danger { background-color: #f8d7da; color: #721c24; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; background: #e2e3e5; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Quản Lý Sự Kiện Câu Lạc Bộ Sinh Viên</h1>
        <p><em>(Bài thực hành cá nhân Buổi 2 - Môn Lập trình Web)</em></p>
        <hr>

        <?php if (!empty($thongBao)): ?>
            <div class="alert"><?= htmlspecialchars($thongBao) ?></div>
        <?php endif; ?>

        <!-- Form nhập thông tin đối tượng -->
        <h2>Thêm Sự Kiện Mới</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label>Tên sự kiện:</label>
                <input type="text" name="ten_su_kien" placeholder="VD: Lễ hội văn hóa sinh viên" required>
            </div>
            <div class="form-group">
                <label>Câu lạc bộ tổ chức:</label>
                <input type="text" name="ten_clb" placeholder="VD: CLB Nghệ thuật" required>
            </div>
            <div class="form-group">
                <label>Ngày diễn ra:</label>
                <input type="date" name="ngay_to_chuc" required>
            </div>
            <div class="form-group">
                <label>Số lượng giới hạn (người):</label>
                <input type="number" name="gioi_han_nguoi" min="1" placeholder="VD: 100" required>
            </div>
            <div class="form-group">
                <label>Số người đã đăng ký trước:</label>
                <input type="number" name="da_dang_ky" min="0" value="0" required>
            </div>
            <button type="submit" class="btn-submit">Thêm sự kiện</button>
        </form>

        <hr style="margin-top: 30px;">

        <!-- Hiển thị danh sách bảng sử dụng vòng lặp -->
        <h2>Danh Sách Sự Kiện Đang Quản Lý</h2>
        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên Sự Kiện</th>
                    <th>CLB Tổ Chức</th>
                    <th>Ngày Diễn Ra</th>
                    <th>Đã ĐK / Giới Hạn</th>
                    <th>Trạng Thái</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $stt = 1;
                foreach ($danhSachSuKien as $item): 
                    $kq = danhGiaTrangThaiSuKien($item['daDangKy'], $item['gioiHan']);
                ?>
                <tr>
                    <td><?= $stt++ ?></td>
                    <td><strong><?= htmlspecialchars($item['ten']) ?></strong></td>
                    <td><?= htmlspecialchars($item['clb']) ?></td>
                    <td><?= htmlspecialchars($item['ngay']) ?></td>
                    <td><?= $item['daDangKy'] ?> / <?= $item['gioiHan'] ?></td>
                    <td>
                        <span class="status-badge <?= $kq['class'] ?>">
                            <?= $kq['status'] ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>