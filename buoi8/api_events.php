<?php
header('Content-Type: application/json; charset=utf-8');

$host = 'localhost';
$dbname = 'web_canhan';
$username = 'root';
$password = '123456';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Xử lý gửi Form thêm mới bằng Fetch POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);

        $title = trim($input['title'] ?? '');
        $club_name = trim($input['club_name'] ?? '');
        $event_date = $input['event_date'] ?? '';
        $max_participants = (int)($input['max_participants'] ?? 0);
        $registered_count = (int)($input['registered_count'] ?? 0);

        if (!empty($title) && !empty($club_name) && !empty($event_date) && $max_participants > 0) {
            $stmt = $pdo->prepare("INSERT INTO events (title, club_name, event_date, max_participants, registered_count) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $club_name, $event_date, $max_participants, $registered_count]);

            echo json_encode(['status' => 'success', 'message' => 'Đã thêm thành công!']);
            exit;
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Dữ liệu gửi lên không hợp lệ!']);
            exit;
        }
    }

    // Mặc định: Trả về danh sách JSON (GET)
    $stmt = $pdo->query("SELECT id, title, club_name, event_date, max_participants, registered_count FROM events ORDER BY id DESC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'total' => count($data),
        'data' => $data
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi máy chủ CSDL: ' . $e->getMessage()
    ]);
}