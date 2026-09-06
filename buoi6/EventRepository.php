<?php
require_once 'Database.php';

class EventRepository {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function getAll($search = '', $limit = 5, $offset = 0) {
        if (!empty($search)) {
            $stmt = $this->pdo->prepare("SELECT * FROM events WHERE title LIKE ? OR club_name LIKE ? ORDER BY id DESC LIMIT ? OFFSET ?");
            $stmt->bindValue(1, "%$search%", PDO::PARAM_STR);
            $stmt->bindValue(2, "%$search%", PDO::PARAM_STR);
            $stmt->bindValue(3, $limit, PDO::PARAM_INT);
            $stmt->bindValue(4, $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        $stmt = $this->pdo->prepare("SELECT * FROM events ORDER BY id DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count($search = '') {
        if (!empty($search)) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM events WHERE title LIKE ? OR club_name LIKE ?");
            $stmt->execute(["%$search%", "%$search%"]);
            return $stmt->fetchColumn();
        }
        return $this->pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO events (title, club_name, event_date, max_participants, registered_count) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$data['title'], $data['club_name'], $data['event_date'], $data['max_participants'], $data['registered_count']]);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE events SET title = ?, club_name = ?, event_date = ?, max_participants = ?, registered_count = ? WHERE id = ?");
        return $stmt->execute([$data['title'], $data['club_name'], $data['event_date'], $data['max_participants'], $data['registered_count'], $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM events WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }
}