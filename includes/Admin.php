<?php
class Admin {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getTotalUsers() {
        $query = "SELECT COUNT(*) as total FROM users WHERE is_admin = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function getTotalMatches() {
        $query = "SELECT COUNT(*) as total FROM matches";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function getMatchStatistics() {
        $query = "SELECT 
                    COUNT(CASE WHEN status = 'Connected' THEN 1 END) as connected,
                    COUNT(CASE WHEN status = 'Interested' THEN 1 END) as interested
                FROM matches";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTodaySignups() {
        $query = "SELECT COUNT(*) as total FROM users 
                WHERE DATE(created_at) = CURDATE() AND is_admin = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function getRecentUsers($limit = 5) {
        $query = "SELECT id, username, email, created_at 
                FROM users 
                WHERE is_admin = 0 
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentMatches($limit = 5) {
        $query = "SELECT m.*, 
                    u1.username as user1_name, 
                    u2.username as user2_name,
                    m.created_at,
                    m.status
                FROM matches m
                JOIN users u1 ON m.user_id = u1.id
                JOIN users u2 ON m.match_user_id = u2.id
                ORDER BY m.created_at DESC
                LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteUser($user_id) {
        try {
            $this->conn->beginTransaction();

            // Delete user's matches
            $query = "DELETE FROM matches WHERE user_id = :user_id OR match_user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();

            // Delete user's notifications
            $query = "DELETE FROM notifications WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();

            // Delete user's profile
            $query = "DELETE FROM profiles WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();

            // Delete user's search filters
            $query = "DELETE FROM search_filters WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();

            // Finally, delete the user
            $query = "DELETE FROM users WHERE id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch(Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function approveProfile($profile_id) {
        $query = "UPDATE profiles SET is_approved = 1 WHERE id = :profile_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":profile_id", $profile_id);
        return $stmt->execute();
    }

    public function getUnapprovedProfiles() {
        $query = "SELECT p.*, u.username, u.email 
                FROM profiles p
                JOIN users u ON p.user_id = u.id
                WHERE p.is_approved = 0
                ORDER BY p.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserStats() {
        $query = "SELECT 
                    COUNT(*) as total_users,
                    COUNT(CASE WHEN p.id IS NOT NULL THEN 1 END) as profiles_created,
                    COUNT(CASE WHEN m.id IS NOT NULL THEN 1 END) as users_with_matches
                FROM users u
                LEFT JOIN profiles p ON u.id = p.user_id
                LEFT JOIN matches m ON u.id = m.user_id OR u.id = m.match_user_id
                WHERE u.is_admin = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllUsersForExport() {
        $query = "SELECT u.id, u.username, u.email, u.created_at,
                         (SELECT COUNT(*) FROM profiles WHERE user_id = u.id) as has_profile,
                         (SELECT COUNT(*) FROM matches WHERE user_id = u.id OR match_user_id = u.id) as matches_count
                  FROM users u
                  WHERE is_admin = 0
                  ORDER BY u.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllMatchesForExport() {
        $query = "SELECT m.id, m.status, m.created_at,
                         u1.username as user1_name,
                         u2.username as user2_name
                  FROM matches m
                  JOIN users u1 ON m.user_id = u1.id
                  JOIN users u2 ON m.match_user_id = u2.id
                  ORDER BY m.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDemographicsForExport() {
        $demographics = [];
        
        // Gender distribution
        $query = "SELECT gender, COUNT(*) as count,
                         (COUNT(*) * 100.0 / (SELECT COUNT(*) FROM profiles)) as percentage
                  FROM profiles
                  GROUP BY gender";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $demographics['gender'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Age distribution
        $query = "SELECT 
                    CASE 
                        WHEN age < 25 THEN '18-24'
                        WHEN age BETWEEN 25 AND 34 THEN '25-34'
                        WHEN age BETWEEN 35 AND 44 THEN '35-44'
                        ELSE '45+'
                    END as age_range,
                    COUNT(*) as count,
                    (COUNT(*) * 100.0 / (SELECT COUNT(*) FROM profiles)) as percentage
                  FROM profiles
                  GROUP BY age_range";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $demographics['age'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Religion distribution
        $query = "SELECT religion, COUNT(*) as count,
                         (COUNT(*) * 100.0 / (SELECT COUNT(*) FROM profiles)) as percentage
                  FROM profiles
                  GROUP BY religion";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $demographics['religion'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $demographics;
    }

    public function getActivityReportData() {
        $query = "SELECT 
                    DATE(created_at) as date,
                    COUNT(DISTINCT CASE WHEN type = 'new_user' THEN user_id END) as new_users,
                    COUNT(DISTINCT CASE WHEN type = 'match' THEN user_id END) as new_matches,
                    COUNT(DISTINCT user_id) as active_users
                  FROM (
                    SELECT user_id, 'new_user' as type, created_at FROM users WHERE is_admin = 0
                    UNION ALL
                    SELECT user_id, 'match' as type, created_at FROM matches
                    UNION ALL
                    SELECT user_id, 'activity' as type, created_at FROM notifications
                  ) as combined_activity
                  WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                  GROUP BY DATE(created_at)
                  ORDER BY date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDailyStats() {
        $query = "SELECT 
                    (SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()) as new_users,
                    (SELECT COUNT(*) FROM matches WHERE DATE(created_at) = CURDATE()) as new_matches,
                    (SELECT COUNT(DISTINCT user_id) FROM notifications WHERE DATE(created_at) = CURDATE()) as active_users,
                    (SELECT 
                        CASE 
                            WHEN yesterday_users = 0 THEN 100
                            ELSE ((today_users - yesterday_users) / yesterday_users) * 100 
                        END
                    FROM (
                        SELECT 
                            (SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()) as today_users,
                            (SELECT COUNT(*) FROM users WHERE DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)) as yesterday_users
                    ) as growth) as user_growth";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getMonthlyStats() {
        $query = "SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as count
                FROM users
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY DATE(created_at)
                ORDER BY date ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGenderDistribution() {
        $query = "SELECT 
                    gender,
                    COUNT(*) as count,
                    (COUNT(*) * 100.0 / (SELECT COUNT(*) FROM profiles)) as percentage
                FROM profiles
                GROUP BY gender";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAgeDistribution() {
        $query = "SELECT 
                    CASE 
                        WHEN age < 25 THEN '18-24'
                        WHEN age BETWEEN 25 AND 34 THEN '25-34'
                        WHEN age BETWEEN 35 AND 44 THEN '35-44'
                        ELSE '45+'
                    END as age_range,
                    COUNT(*) as count,
                    (COUNT(*) * 100.0 / (SELECT COUNT(*) FROM profiles)) as percentage
                FROM profiles
                GROUP BY age_range
                ORDER BY MIN(age)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReligionDistribution() {
        $query = "SELECT 
                    religion,
                    COUNT(*) as count,
                    (COUNT(*) * 100.0 / (SELECT COUNT(*) FROM profiles)) as percentage
                FROM profiles
                GROUP BY religion";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMatchSuccessRate() {
        $query = "SELECT 
                    (COUNT(CASE WHEN status = 'Connected' THEN 1 END) * 100.0 / COUNT(*)) as success_rate
                FROM matches";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return round($result['success_rate'], 1);
    }

    public function getAllUsers($page = 1, $limit = 15, $search = '', $filter = '') {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT u.*, 
                         (SELECT COUNT(*) FROM profiles WHERE user_id = u.id) as has_profile,
                         (SELECT COUNT(*) FROM matches WHERE user_id = u.id OR match_user_id = u.id) as matches_count
                  FROM users u
                  WHERE is_admin = 0";
        
        if(!empty($search)) {
            $query .= " AND (username LIKE :search OR email LIKE :search)";
        }
        
        if(!empty($filter)) {
            switch($filter) {
                case 'active':
                    $query .= " AND id IN (SELECT DISTINCT user_id FROM profiles)";
                    break;
                case 'inactive':
                    $query .= " AND id NOT IN (SELECT DISTINCT user_id FROM profiles)";
                    break;
                case 'banned':
                    $query .= " AND is_banned = 1";
                    break;
            }
        }
        
        $query .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        
        if(!empty($search)) {
            $searchTerm = "%{$search}%";
            $stmt->bindParam(":search", $searchTerm);
        }
        
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProfilesForModeration($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT p.*, u.username, u.email 
                  FROM profiles p
                  JOIN users u ON p.user_id = u.id
                  WHERE p.is_approved = 0
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReports($page = 1, $limit = 10, $filter = 'pending') {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT r.*, 
                         u1.username as reporter_username,
                         u2.username as reported_username,
                         u2.email as reported_email,
                         p.photo_url as reported_user_photo
                  FROM reports r
                  JOIN users u1 ON r.reporter_id = u1.id
                  JOIN users u2 ON r.reported_user_id = u2.id
                  LEFT JOIN profiles p ON r.reported_user_id = p.user_id
                  WHERE 1=1";
        
        if($filter !== 'all') {
            $query .= " AND r.status = :status";
        }
        
        $query .= " ORDER BY r.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        
        if($filter !== 'all') {
            $stmt->bindParam(":status", $filter);
        }
        
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSiteSettings() {
        $query = "SELECT * FROM site_settings WHERE id = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateSiteSettings($settings) {
        $query = "UPDATE site_settings SET
                  site_name = :site_name,
                  site_description = :site_description,
                  maintenance_mode = :maintenance_mode
                  WHERE id = 1";
                  
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":site_name", $settings['site_name']);
        $stmt->bindParam(":site_description", $settings['site_description']);
        $stmt->bindParam(":maintenance_mode", $settings['maintenance_mode']);
        
        return $stmt->execute();
    }

    public function getEmailSettings() {
        $query = "SELECT * FROM email_settings WHERE id = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateEmailSettings($settings) {
        $query = "UPDATE email_settings SET
                  smtp_host = :smtp_host,
                  smtp_port = :smtp_port,
                  smtp_username = :smtp_username,
                  smtp_password = :smtp_password
                  WHERE id = 1";
                  
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":smtp_host", $settings['smtp_host']);
        $stmt->bindParam(":smtp_port", $settings['smtp_port']);
        $stmt->bindParam(":smtp_username", $settings['smtp_username']);
        $stmt->bindParam(":smtp_password", $settings['smtp_password']);
        
        return $stmt->execute();
    }

    public function getSecuritySettings() {
        $query = "SELECT * FROM security_settings WHERE id = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateSecuritySettings($settings) {
        $query = "UPDATE security_settings SET
                  min_password_length = :min_password_length,
                  max_login_attempts = :max_login_attempts,
                  enable_2fa = :enable_2fa
                  WHERE id = 1";
                  
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":min_password_length", $settings['min_password_length']);
        $stmt->bindParam(":max_login_attempts", $settings['max_login_attempts']);
        $stmt->bindParam(":enable_2fa", $settings['enable_2fa']);
        
        return $stmt->execute();
    }

    public function resolveReport($report_id) {
        $query = "UPDATE reports SET status = 'resolved' WHERE id = :report_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":report_id", $report_id);
        return $stmt->execute();
    }

    public function deleteReport($report_id) {
        $query = "DELETE FROM reports WHERE id = :report_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":report_id", $report_id);
        return $stmt->execute();
    }

    public function banUser($user_id) {
        $query = "UPDATE users SET is_banned = 1 WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }

    public function unbanUser($user_id) {
        $query = "UPDATE users SET is_banned = 0 WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }

    public function getTotalPendingProfiles() {
        $query = "SELECT COUNT(*) as total 
                  FROM profiles 
                  WHERE is_approved = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function getTotalReports($filter = 'pending') {
        $query = "SELECT COUNT(*) as total 
                  FROM reports 
                  WHERE 1=1";
        
        if($filter !== 'all') {
            $query .= " AND status = :status";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if($filter !== 'all') {
            $stmt->bindParam(":status", $filter);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?> 