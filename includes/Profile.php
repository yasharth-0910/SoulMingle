<?php
class Profile {
    private $conn;
    private $table_name = "profiles";

    public $id;
    public $user_id;
    public $age;
    public $gender;
    public $religion;
    public $education;
    public $interests;
    public $photo_url;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    user_id = :user_id,
                    age = :age,
                    gender = :gender,
                    religion = :religion,
                    education = :education,
                    interests = :interests,
                    photo_url = :photo_url";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->sanitizeInputs();

        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":age", $this->age);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":religion", $this->religion);
        $stmt->bindParam(":education", $this->education);
        $stmt->bindParam(":interests", $this->interests);
        $stmt->bindParam(":photo_url", $this->photo_url);

        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    age = :age,
                    gender = :gender,
                    religion = :religion,
                    education = :education,
                    interests = :interests,
                    photo_url = :photo_url
                WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->sanitizeInputs();

        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":age", $this->age);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":religion", $this->religion);
        $stmt->bindParam(":education", $this->education);
        $stmt->bindParam(":interests", $this->interests);
        $stmt->bindParam(":photo_url", $this->photo_url);

        return $stmt->execute();
    }

    public function getByUserId($user_id) {
        $query = "SELECT p.*, u.username 
                  FROM " . $this->table_name . " p
                  JOIN users u ON p.user_id = u.id
                  WHERE p.user_id = ?
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->username = $row['username'];
            $this->age = $row['age'];
            $this->gender = $row['gender'];
            $this->religion = $row['religion'];
            $this->education = $row['education'];
            $this->interests = $row['interests'];
            $this->photo_url = $row['photo_url'];
            
            return true;
        }
        return false;
    }

    private function sanitizeInputs() {
        $this->age = htmlspecialchars(strip_tags($this->age));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->religion = htmlspecialchars(strip_tags($this->religion));
        $this->education = htmlspecialchars(strip_tags($this->education));
        $this->interests = htmlspecialchars(strip_tags($this->interests));
        $this->photo_url = htmlspecialchars(strip_tags($this->photo_url));
    }

    private function loadFromArray($data) {
        $this->id = $data['id'];
        $this->user_id = $data['user_id'];
        $this->age = $data['age'];
        $this->gender = $data['gender'];
        $this->religion = $data['religion'];
        $this->education = $data['education'];
        $this->interests = $data['interests'];
        $this->photo_url = $data['photo_url'];
    }

    public function updatePhoto($photo_url) {
        $query = "UPDATE " . $this->table_name . "
                SET photo_url = :photo_url
                WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":photo_url", $photo_url);
        $stmt->bindParam(":user_id", $this->user_id);

        return $stmt->execute();
    }
}
?> 