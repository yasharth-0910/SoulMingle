<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $email;
    public $password;
    public $is_admin = false;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    username = :username,
                    email = :email,
                    password_hash = :password";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        // Hash password
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

        // Bind values
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $password_hash);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function emailExists() {
        $query = "SELECT id, username, password_hash, is_admin
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->password_hash = $row['password_hash'];
            $this->is_admin = (bool)$row['is_admin'];
            return true;
        }
        return false;
    }

    public function login() {
        // Check if email exists
        if ($this->emailExists()) {
            // Verify password
            if (password_verify($this->password, $this->password_hash)) {
                return true;
            }
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    username = :username,
                    email = :email
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind values
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    public function updatePassword() {
        $query = "UPDATE " . $this->table_name . "
                SET password_hash = :password
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }
}
?> 