<?php
class Database {
    // Database credentials
    private $host = "sdb-81.hosting.stackcp.net";
    private $db_name = "jumandigas-353038374f79";
    private $username = "jumandigas";
    private $password = "ks2bs8a8ak";
    public $conn;

    // Get database connection
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }

        return $this->conn;
    }
}
?>

