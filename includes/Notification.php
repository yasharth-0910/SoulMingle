<?php
class Notification {
    private $conn;
    private $table_name = "notifications";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($user_id, $type, $message, $related_id = null) {
        $query = "INSERT INTO " . $this->table_name . "
                (user_id, type, message, related_id, is_read)
                VALUES (:user_id, :type, :message, :related_id, 0)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":type", $type);
        $stmt->bindParam(":message", $message);
        $stmt->bindParam(":related_id", $related_id);

        return $stmt->execute();
    }

    public function getUnreadNotifications($user_id) {
        $query = "SELECT * FROM " . $this->table_name . "
                WHERE user_id = :user_id AND is_read = 0
                ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead($notification_id) {
        $query = "UPDATE " . $this->table_name . "
                SET is_read = 1
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $notification_id);
        return $stmt->execute();
    }
}
?> 