<?php

class Database
{
    private $host = "localhost";
    private $db_name = "events_management_system";
    private $username = "root";
    private $password = "mysql";
    private $conn;

    public function connect()
    {
        if (!$this->conn) {
            try {
                $this->conn = new PDO("mysql:host=$this->host;dbname=$this->db_name", $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database Connection Failed: " . $e->getMessage());
            }
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
