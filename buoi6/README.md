# Báo cáo Buổi 6: Refactor CRUD sang MVC & Repository Pattern

## So sánh Trước và Sau khi Refactor

| Tiêu chí | Trước khi Refactor (Buổi 5) | Sau khi Refactor (Buổi 6) |
| :--- | :--- | :--- |
| *Cấu trúc code** | Trộn lẫn logic DB, xử lý Form và giao diện HTML trong duy nhất file `index.php`. | Tách biệt thành các tầng: Model (`EventRepository`), Controller (`EventController`), View (`views/event_list.php`). |
| *Tái sử dụng DB** | Viết câu lệnh PDO trực tiếp ở từng file. | Tập trung kết nối tại `Database.php`, các truy vấn đóng gói trong hàm của `EventRepository`. |
| *Bảo trì & Mở rộng** | Khó bảo trì khi ứng dụng phình to. | Dễ bảo trì, dễ viết kiểm thử độc lập cho Repository hoặc thay đổi giao diện ở View. | 
