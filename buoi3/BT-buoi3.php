<?php
// Khởi tạo session để lưu trữ danh sách sự kiện qua các lần submit
session_start();

// 1. Hàm đánh giá trạng thái sự kiện (Kế thừa từ Buổi 2)
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

// 2. Dữ liệu mặc định ban đầu
if (!isset($_SESSION['danhSachSuKien'])) {
    $_SESSION['danhSachSuKien'] = [
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
}

// 3. Biến quản lý trạng thái form và lỗi
$errors = [];
$thongBaoThanhCong = '';

// Biến lưu giữ dữ liệu nhập lại (Old input)
$tenSuKien = '';
$tenCLB = '';
$ngayToChuc = '';
$gioiHanNguoi = '';
$soDaDangKy = '';

// 4. Tiếp nhận, Chuẩn hóa & Validate Form khi có POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Chuẩn hóa đầu vào (Sanitize)
    $tenSuKien = isset($_POST['ten_su_kien']) ? trim($_POST['ten_su_kien']) : '';
    $tenCLB = isset($_POST['ten_clb']) ? trim($_POST['ten_clb']) : '';
    $ngayToChuc = isset($_POST['ngay_to_chuc']) ? trim($_POST['ngay_to_chuc']) : '';
    $gioiHanNguoi = isset($_POST['gioi_han_nguoi']) ? trim($_POST['gioi_han_nguoi']) : '';
    $soDaDangKy = isset($_POST['da_dang_ky']) ? trim($_POST['da_dang_ky']) : '';

    // --- Validation: Tên sự kiện ---
    if (empty($tenSuKien)) {
        $errors['ten_su_kien'] = 'Vui lòng nhập tên sự kiện.';
    } elseif (mb_strlen($tenSuKien) < 5 || mb_strlen($tenSuKien) > 150) {
        $errors['ten_su_kien'] = 'Tên sự kiện phải từ 5 đến 150 ký tự.';
    }

    // --- Validation: Tên câu lạc bộ ---
    if (empty($tenCLB)) {
        $errors['ten_clb'] = 'Vui lòng nhập tên câu lạc bộ tổ chức.';
    } elseif (mb_strlen($tenCLB) < 3 || mb_strlen($tenCLB) > 100) {
        $errors['ten_clb'] = 'Tên CLB phải từ 3 đến 100 ký tự.';
    }

    // --- Validation: Ngày tổ chức ---
    if (empty($ngayToChuc)) {
        $errors['ngay_to_chuc'] = 'Vui lòng chọn ngày diễn ra sự kiện.';
    } else {
        $d = DateTime::createFromFormat('Y-m-d', $ngayToChuc);
        if (!($d && $d->format('Y-m-d') === $ngayToChuc)) {
            $errors['ngay_to_chuc'] = 'Định dạng ngày không hợp lệ.';
        }
    }

    // --- Validation: Giới hạn người tham gia ---
    if ($gioiHanNguoi === '') {
        $errors['gioi_han_nguoi'] = 'Vui lòng nhập giới hạn số người.';
    } elseif (!filter_var($gioiHanNguoi, FILTER_VALIDATE_INT) || (int)$gioiHanNguoi <= 0) {
        $errors['gioi_han_nguoi'] = 'Giới hạn người phải là số nguyên dương lớn hơn 0.';
    } elseif ((int)$gioiHanNguoi > 10000) {
        $errors['gioi_han_nguoi'] = 'Quy mô sự kiện không được vượt quá 10,000 người.';
    }

    // --- Validation: Số lượng đã đăng ký & logic nghiệp vụ ---
    if ($soDaDangKy === '') {
        $errors['da_dang_ky'] = 'Vui lòng nhập số người đã đăng ký.';
    } elseif (!filter_var($soDaDangKy, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0]])) {
        $errors['da_dang_ky'] = 'Số người đã đăng ký phải là số nguyên không âm (≥ 0).';
    } elseif (empty($errors['gioi_han_nguoi']) && (int)$soDaDangKy > (int)$gioiHanNguoi) {
        $errors['da_dang_ky'] = 'Số người đăng ký trước không thể lớn hơn giới hạn người tham gia.';
    }

    // --- Xử lý khi không có lỗi ---
    if (empty($errors)) {
        $_SESSION['danhSachSuKien'][] = [
            'ten' => $tenSuKien,
            'clb' => $tenCLB,
            'ngay' => $ngayToChuc,
            'gioiHan' => (int)$gioiHanNguoi,
            'daDangKy' => (int)$soDaDangKy
        ];
        
        $thongBaoThanhCong = 'Thêm sự kiện mới thành công!';
        
        // Reset form sau khi gửi thành công
        $tenSuKien = '';
        $tenCLB = '';
        $ngayToChuc = '';
        $gioiHanNguoi = '';
        $soDaDangKy = '';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buổi 3 - Form Quản Lý Sự Kiện An Toàn</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f4f6f9; margin: 0; padding: 30px; }
        .container { max-width: 900px; margin: 0 auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.08); }
        h1, h2 { color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; }
        input { width: 100%; padding: 9px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        input.is-invalid { border-color: #dc3545; background-color: #fff8f8; }
        .error-message { color: #dc3545; font-size: 13px; margin-top: 4px; display: block; }
        .btn-submit { background-color: #007bff; color: white; border: none; padding: 10px 20px; font-size: 15px; border-radius: 4px; cursor: pointer; }
        .btn-submit:hover { background-color: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #dee2e6; padding: 10px; text-align: left; }
        th { background-color: #f1f3f5; color: #495057; }
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 13px; font-weight: 600; }
        .success { background-color: #d4edda; color: #155724; }
        .warning { background-color: #fff3cd; color: #856404; }
        .danger { background-color: #f8d7da; color: #721c24; }
        .alert-success { padding: 12px; margin-bottom: 20px; border-radius: 4px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Quản Lý Sự Kiện CLB Sinh Viên</h1>
        <p><em>(Bài thực hành Buổi 3: Form an toàn & Kiểm tra tính hợp lệ phía Server)</em></p>
        <hr>

        <?php if (!empty($thongBaoThanhCong)): ?>
            <div class="alert-success"><?= htmlspecialchars($thongBaoThanhCong, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <h2>Thêm Sự Kiện Mới</h2>
        <!-- Tắt validate mặc định của trình duyệt để kiểm thử server-side: novalidate -->
        <form method="POST" action="" novalidate>
            <div class="form-group">
                <label for="ten_su_kien">Tên sự kiện <span style="color:red">*</span>:</label>
                <input type="text" id="ten_su_kien" name="ten_su_kien" 
                       class="<?= isset($errors['ten_su_kien']) ? 'is-invalid' : '' ?>" 
                       value="<?= htmlspecialchars($tenSuKien, ENT_QUOTES, 'UTF-8') ?>" 
                       placeholder="VD: Lễ hội văn hóa sinh viên">
                <?php if (isset($errors['ten_su_kien'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['ten_su_kien'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="ten_clb">Câu lạc bộ tổ chức <span style="color:red">*</span>:</label>
                <input type="text" id="ten_clb" name="ten_clb" 
                       class="<?= isset($errors['ten_clb']) ? 'is-invalid' : '' ?>" 
                       value="<?= htmlspecialchars($tenCLB, ENT_QUOTES, 'UTF-8') ?>" 
                       placeholder="VD: CLB Nghệ thuật">
                <?php if (isset($errors['ten_clb'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['ten_clb'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="ngay_to_chuc">Ngày diễn ra <span style="color:red">*</span>:</label>
                <input type="date" id="ngay_to_chuc" name="ngay_to_chuc" 
                       class="<?= isset($errors['ngay_to_chuc']) ? 'is-invalid' : '' ?>" 
                       value="<?= htmlspecialchars($ngayToChuc, ENT_QUOTES, 'UTF-8') ?>">
                <?php if (isset($errors['ngay_to_chuc'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['ngay_to_chuc'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="gioi_han_nguoi">Số lượng giới hạn (người) <span style="color:red">*</span>:</label>
                <input type="number" id="gioi_han_nguoi" name="gioi_han_nguoi" 
                       class="<?= isset($errors['gioi_han_nguoi']) ? 'is-invalid' : '' ?>" 
                       value="<?= htmlspecialchars($gioiHanNguoi, ENT_QUOTES, 'UTF-8') ?>" 
                       placeholder="VD: 100">
                <?php if (isset($errors['gioi_han_nguoi'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['gioi_han_nguoi'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="da_dang_ky">Số người đã đăng ký trước <span style="color:red">*</span>:</label>
                <input type="number" id="da_dang_ky" name="da_dang_ky" 
                       class="<?= isset($errors['da_dang_ky']) ? 'is-invalid' : '' ?>" 
                       value="<?= htmlspecialchars($soDaDangKy, ENT_QUOTES, 'UTF-8') ?>" 
                       placeholder="VD: 0">
                <?php if (isset($errors['da_dang_ky'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['da_dang_ky'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-submit">Thêm sự kiện</button>
        </form>

        <hr style="margin-top: 30px;">

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
                foreach ($_SESSION['danhSachSuKien'] as $item): 
                    $kq = danhGiaTrangThaiSuKien($item['daDangKy'], $item['gioiHan']);
                ?>
                <tr>
                    <td><?= $stt++ ?></td>
                    <td><strong><?= htmlspecialchars($item['ten'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                    <td><?= htmlspecialchars($item['clb'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($item['ngay'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($item['daDangKy'], ENT_QUOTES, 'UTF-8') ?> / <?= htmlspecialchars($item['gioiHan'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <span class="status-badge <?= htmlspecialchars($kq['class'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($kq['status'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>