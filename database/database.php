<?php

class Database
{
    private $host = "localhost";  // Your database host
    private $db_name = "events_management_system";  // Your database name
    private $username = "root";  // Your database username
    private $password = "mysql";  // Your database password
    private $conn;

    public function connect()
    {
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->db_name", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
        return $this->conn;
    }

    public function query($sql, $params = [], $returnLastInsertId = false)
    {
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute($params);

        if ($returnLastInsertId) {
            return $this->connect()->lastInsertId();
        }

        return $stmt;
    }
}
