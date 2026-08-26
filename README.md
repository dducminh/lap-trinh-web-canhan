 Kết Quả Chạy Thử Nghiệm Truy Vấn SQL
- Truy vấn 1: Danh sách sự kiện cùng CLB ! <img width="1500" height="678" alt="query_1_danh_sach_su_kien" src="https://github.com/user-attachments/assets/7a6d2347-70c0-4308-9d02-468d56231082" />

- Truy vấn 2: Thống kê số lượng đăng ký theo sự kiện ! <img width="1520" height="713" alt="query_2_thong_ke_dang_ky" src="https://github.com/user-attachments/assets/277dd7f8-ac7e-4d59-992d-9d178e3c18c0" />

- Truy vấn 3: Danh sách sinh viên đăng ký CLB Tin học ! <img width="1486" height="670" alt="query_3_loc_clb_tin_hoc" src="https://github.com/user-attachments/assets/dbb1f1e4-578c-4c56-bf83-c9841ffc60a5" />

---

 Báo Cáo Thực Hành Buổi 5 - PDO & CRUD Cá Nhân

 Bảng 5 Test Case kiểm thử nhập liệu

| STT | Tên Test Case | Dữ liệu đầu vào | Kỳ vọng | Kết quả thực tế |
| :--- | :--- | :--- | :--- | :--- |
| **TC01** | Nhập hợp lệ đầy đủ | Title: "Hội thảo AI", CLB: "Tin học", Date: "2026-11-10", Max: 100, Reg: 10 | Thêm mới thành công, hiển thị vào bảng | Đạt (Success) |
| **TC02** | Để trống Tên sự kiện | Title: "", CLB: "Tin học", Date: "2026-11-10", Max: 100, Reg: 10 | Báo lỗi không hợp lệ, không lưu | Đạt (Validation Error) |
| **TC03** | Giới hạn số người <= 0 | Title: "Workshop", CLB: "Kỹ năng", Date: "2026-11-10", Max: -5, Reg: 0 | Form HTML5 chặn hoặc PHP trả về lỗi | Đạt (Blocked) |
| **TC04** | Đã đăng ký là số âm | Title: "Giao lưu", CLB: "Nghệ thuật", Date: "2026-11-10", Max: 50, Reg: -2 | Form chặn min=0, PHP từ chối | Đạt (Blocked) |
| **TC05** | Tìm kiếm từ khóa | Gõ từ khóa `Tin học` vào ô tìm kiếm | Bảng chỉ lọc ra các sự kiện của CLB Tin học | Đạt (Filter matched) |
