<?php
class Search {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function searchProfiles($filters, $user_id, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT p.*, u.username, u.email 
                FROM profiles p
                JOIN users u ON p.user_id = u.id
                WHERE p.user_id != :user_id ";
        
        $params = [":user_id" => $user_id];
        
        // Add filters
        if (!empty($filters['min_age'])) {
            $query .= "AND p.age >= :min_age ";
            $params[':min_age'] = $filters['min_age'];
        }
        
        if (!empty($filters['max_age'])) {
            $query .= "AND p.age <= :max_age ";
            $params[':max_age'] = $filters['max_age'];
        }
        
        if (!empty($filters['gender'])) {
            $query .= "AND p.gender = :gender ";
            $params[':gender'] = $filters['gender'];
        }
        
        if (!empty($filters['religion'])) {
            $query .= "AND p.religion = :religion ";
            $params[':religion'] = $filters['religion'];
        }
        
        if (!empty($filters['education'])) {
            $query .= "AND p.education LIKE :education ";
            $params[':education'] = '%' . $filters['education'] . '%';
        }
        
        $query .= "ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;
        
        $stmt = $this->conn->prepare($query);
        
        foreach($params as $key => &$value) {
            if($key == ':limit' || $key == ':offset') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalResults($filters, $user_id) {
        $query = "SELECT COUNT(*) as total 
                FROM profiles p
                JOIN users u ON p.user_id = u.id
                WHERE p.user_id != :user_id ";
        
        $params = [":user_id" => $user_id];
        
        // Add filters
        if (!empty($filters['min_age'])) {
            $query .= "AND p.age >= :min_age ";
            $params[':min_age'] = $filters['min_age'];
        }
        
        if (!empty($filters['max_age'])) {
            $query .= "AND p.age <= :max_age ";
            $params[':max_age'] = $filters['max_age'];
        }
        
        if (!empty($filters['gender'])) {
            $query .= "AND p.gender = :gender ";
            $params[':gender'] = $filters['gender'];
        }
        
        if (!empty($filters['religion'])) {
            $query .= "AND p.religion = :religion ";
            $params[':religion'] = $filters['religion'];
        }
        
        if (!empty($filters['education'])) {
            $query .= "AND p.education LIKE :education ";
            $params[':education'] = '%' . $filters['education'] . '%';
        }
        
        $stmt = $this->conn->prepare($query);
        
        foreach($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function saveSearchFilters($user_id, $filters) {
        $query = "INSERT INTO search_filters (
                    user_id, min_age, max_age, religion, education
                ) VALUES (
                    :user_id, :min_age, :max_age, :religion, :education
                )
                ON DUPLICATE KEY UPDATE
                    min_age = :min_age,
                    max_age = :max_age,
                    religion = :religion,
                    education = :education";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":min_age", $filters['min_age']);
        $stmt->bindParam(":max_age", $filters['max_age']);
        $stmt->bindParam(":religion", $filters['religion']);
        $stmt->bindParam(":education", $filters['education']);
        
        return $stmt->execute();
    }
}
?> 