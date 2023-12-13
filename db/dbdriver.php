<?php
class DBDriver {
    private $servername;
    private $username;
    private $password;
    private $dbname;
    private $conn;
    public function __construct(string $servername, string $username, string $password, string $dbname) {
        $this->servername = $servername;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
    }

    // Create connection, if it fails it'll throw an exception
    public function connect() {
        $this->conn = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);
        if (!$this->conn) {
            throw new Exception("". mysqli_connect_error());
        }
    }
    // Should a transaction be started here or in the function that uses this function?
    public function query(string $sql, ...$params): mysqli_result{
        if (!$this->conn) {
            throw new Exception("Currently not connected to a database");
        }
        $stmt = $this->conn->prepare($sql);
        print_r($params);
        $success = $stmt->execute($params);
        if (!$success) {
            throw new Exception("". mysqli_error($this->conn));
        }
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }
}
?>