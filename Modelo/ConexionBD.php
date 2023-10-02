<?php
class DatabaseConnection {
    private $host = "127.0.0.1";
    private $port = 3306;
    private $socket = "";
    private $user = "root";
    private $password = "";
    private $dbname = "Permanencia_Desercion";
    private $conn;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->dbname, $this->port, $this->socket);
        
        if ($this->conn->connect_error) {
            throw new Exception("No se puede establecer la conexión: " . $this->conn->connect_error);
        }
    }

    public function getDBConnection() {
        return $this->conn;
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>