# Báo cáo Buổi 8: Fetch API, Endpoint JSON & Kiểm Thử

## Checklist kiểm thử chức năng cá nhân
- [x] *Endpoint JSON (`api_events.php`):** Trả về HTTP status 200, Content-Type: `application/json`, cấu trúc dữ liệu gồm `status`, `total` và mảng `data`.
- [x] *Trạng thái Đang tải (Loading):** Hiển thị dòng thông báo loading trước khi dữ liệu được nạp xong.
- [x] *Trạng thái Lỗi (Error):** Khi tắt MySQL hoặc URL endpoint sai, giao diện bắt khối `catch` và hiển thị hộp thông báo lỗi màu đỏ rõ ràng thay vì làm sập trang.
- [x] *Hiển thị dữ liệu:** Render danh sách sự kiện vào bảng DOM HTML đầy đủ và chính xác.