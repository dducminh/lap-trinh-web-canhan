# Báo cáo Buổi 7: Authentication, Session Guard & Test Case

## Danh sách tài khoản thử nghiệm
*Admin:** `admin` / Mật khẩu: `123456` (Quyền quản trị viên)
*Student:** `student` / Mật khẩu: `123456` (Quyền người dùng thông thường)

## Test case: Truy cập URL trực tiếp khi chưa đăng nhập
*Kịch bản:** Mở tab ẩn danh, chưa đăng nhập bất kỳ tài khoản nào, gõ trực tiếp URL `http://localhost/lap-trinh-web-canhan/buoi7/dashboard.php`.
*Kết quả mong muốn:** Server Guard bắt được biến session rỗng, chặn không render nội dung và dùng `header("Location: login.php")` điều hướng người dùng về trang đăng nhập.
*Kết quả thực tế:** Người dùng lập tức bị chuyển hướng về `login.php?error=unauthorized`. Đạt (Pass).