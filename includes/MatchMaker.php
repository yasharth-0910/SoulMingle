<?php
require_once "Notification.php";

class MatchMaker {
    private $conn;
    private $table_name = "matches";
    private $notification;

    public function __construct($db) {
        $this->conn = $db;
        $this->notification = new Notification($db);
    }

    public function expressInterest($user_id, $match_user_id) {
        // Check if interest already exists
        if($this->interestExists($user_id, $match_user_id)) {
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . "
                (user_id, match_user_id, status)
                VALUES (:user_id, :match_user_id, 'Interested')";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":match_user_id", $match_user_id);

        if($stmt->execute()) {
            // Create notification for the match user
            $this->notification->create(
                $match_user_id,
                'interest',
                'Someone has expressed interest in your profile!',
                $user_id
            );

            // Check if there's mutual interest
            if($this->checkMutualInterest($user_id, $match_user_id)) {
                $this->updateMatchStatus($user_id, $match_user_id, 'Connected');
                
                // Create notifications for both users
                $this->notification->create(
                    $user_id,
                    'match',
                    "You have a new match! Start connecting now!",
                    $match_user_id
                );
                $this->notification->create(
                    $match_user_id,
                    'match',
                    "You have a new match! Start connecting now!",
                    $user_id
                );
                
                return 'Connected';
            }
            return 'Interested';
        }
        return false;
    }

    private function interestExists($user_id, $match_user_id) {
        $query = "SELECT id FROM " . $this->table_name . "
                WHERE user_id = :user_id AND match_user_id = :match_user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":match_user_id", $match_user_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    private function checkMutualInterest($user_id, $match_user_id) {
        $query = "SELECT id FROM " . $this->table_name . "
                WHERE user_id = :match_user_id AND match_user_id = :user_id
                AND status = 'Interested'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":match_user_id", $match_user_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    private function updateMatchStatus($user_id, $match_user_id, $status) {
        $query = "UPDATE " . $this->table_name . "
                SET status = :status
                WHERE (user_id = :user_id AND match_user_id = :match_user_id)
                OR (user_id = :match_user_id AND match_user_id = :user_id)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":match_user_id", $match_user_id);
        
        return $stmt->execute();
    }

    public function getMatches($user_id, $status = null) {
        $query = "SELECT m.*, p.*, u.username, u.email 
                FROM " . $this->table_name . " m
                JOIN profiles p ON p.user_id = 
                    CASE 
                        WHEN m.user_id = :user_id THEN m.match_user_id
                        ELSE m.user_id
                    END
                JOIN users u ON u.id = p.user_id
                WHERE (m.user_id = :user_id OR m.match_user_id = :user_id)";

        if($status) {
            $query .= " AND m.status = :status";
        }

        $query .= " ORDER BY m.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        
        if($status) {
            $stmt->bindParam(":status", $status);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?> 