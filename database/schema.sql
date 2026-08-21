CREATE DATABASE IF NOT EXISTS quan_ly_su_kien_clb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quan_ly_su_kien_clb;

CREATE TABLE IF NOT EXISTS cau_lac_bo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_clb VARCHAR(100) NOT NULL UNIQUE,
    mo_ta TEXT NULL,
    email_lien_he VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS su_kien (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clb_id INT NOT NULL,
    ten_su_kien VARCHAR(150) NOT NULL,
    ngay_to_chuc DATE NOT NULL,
    gioi_han_nguoi INT NOT NULL CHECK (gioi_han_nguoi > 0),
    dia_diem VARCHAR(200) DEFAULT 'Hội trường A',
    trang_thai ENUM('sap_dien_ra', 'dang_dien_ra', 'da_ket_thuc') DEFAULT 'sap_dien_ra',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sukien_clb FOREIGN KEY (clb_id) REFERENCES cau_lac_bo(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS dang_ky (
    id INT AUTO_INCREMENT PRIMARY KEY,
    su_kien_id INT NOT NULL,
    ho_ten_sinh_vien VARCHAR(100) NOT NULL,
    ma_sinh_vien VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    thoi_gian_dang_ky TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_dangky_sukien FOREIGN KEY (su_kien_id) REFERENCES su_kien(id) ON DELETE CASCADE,
    CONSTRAINT uq_sinhvien_sukien UNIQUE (su_kien_id, ma_sinh_vien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
