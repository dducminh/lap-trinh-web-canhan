<?php
class Database {
    public static function getConnection() {
        $host = 'localhost';
        $dbname = 'web_canhan';
        $username = 'root';
        $password = ''; // Điền mật khẩu MySQL nếu có
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Lỗi kết nối CSDL: " . $e->getMessage());
        }
    }
}