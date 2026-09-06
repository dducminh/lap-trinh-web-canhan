<?php
require_once 'EventRepository.php';

class EventController {
    private $repo;

    public function __construct() {
        $this->repo = new EventRepository();
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? 'index';
        $message = '';
        $editEvent = null;

        if ($action === 'delete' && isset($_GET['id'])) {
            $this->repo->delete($_GET['id']);
            header("Location: index.php?msg=deleted");
            exit;
        }

        if ($action === 'edit' && isset($_GET['id'])) {
            $editEvent = $this->repo->getById($_GET['id']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'club_name' => trim($_POST['club_name'] ?? ''),
                'event_date' => $_POST['event_date'] ?? '',
                'max_participants' => (int)($_POST['max_participants'] ?? 0),
                'registered_count' => (int)($_POST['registered_count'] ?? 0),
            ];
            $id = isset($_POST['id']) ? (int)$_POST['id'] : null;

            if (!empty($data['title']) && !empty($data['club_name']) && $data['max_participants'] > 0) {
                if ($id) {
                    $this->repo->update($id, $data);
                    $message = "Cập nhật thành công!";
                    $editEvent = null;
                } else {
                    $this->repo->create($data);
                    $message = "Thêm thành công!";
                }
            } else {
                $message = "Dữ liệu không hợp lệ!";
            }
        }

        $search = trim($_GET['search'] ?? '');
        $limit = 4;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $events = $this->repo->getAll($search, $limit, $offset);
        $total = $this->repo->count($search);
        $totalPages = ceil($total / $limit);

        require 'views/event_list.php';
    }
}