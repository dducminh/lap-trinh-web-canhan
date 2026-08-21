USE quan_ly_su_kien_clb;

INSERT INTO cau_lac_bo (id, ten_clb, mo_ta, email_lien_he) VALUES
(1, 'CLB Tin học', 'Nơi giao lưu, chia sẻ kiến thức CNTT và lập trình phần mềm', 'clbtinhoc@school.edu.vn'),
(2, 'CLB Tình nguyện', 'Tổ chức các hoạt động thiện nguyện vì cộng đồng sinh viên', 'clbtinhnguyen@school.edu.vn'),
(3, 'CLB Nghệ thuật', 'Phát triển năng khiếu âm nhạc, vũ đạo và mỹ thuật', 'clbnghethuat@school.edu.vn');

INSERT INTO su_kien (id, clb_id, ten_su_kien, ngay_to_chuc, gioi_han_nguoi, dia_diem, trang_thai) VALUES
(1, 2, 'Chào tân sinh viên 2026', '2026-09-15', 200, 'Hội trường Lớn', 'sap_dien_ra'),
(2, 1, 'Workshop Git & GitHub Cơ bản', '2026-09-20', 80, 'Phòng thực hành Lab 3', 'sap_dien_ra'),
(3, 3, 'Đêm nhạc acoustic Mùa Thu', '2026-10-05', 120, 'Sân khấu ngoài trời', 'sap_dien_ra');

INSERT INTO dang_ky (su_kien_id, ho_ten_sinh_vien, ma_sinh_vien, email) VALUES
(1, 'Nguyễn Văn An', 'SV2026001', 'an.nv@school.edu.vn'),
(1, 'Trần Thị Bích', 'SV2026002', 'bich.tt@school.edu.vn'),
(2, 'Nguyễn Văn An', 'SV2026001', 'an.nv@school.edu.vn');
