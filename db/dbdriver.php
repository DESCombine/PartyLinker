<?php
/**
 * Class for handling database connections and queries
 * @property string $servername
 * @property string $username
 * @property string $password
 * @property string $dbname
 * @property mysqli $conn
 * @method void connect()
 * @method mysqli_result|bool query(string $sql, ...$params)
 * @method void close_connection()
 * @method int get_last_inserted_id()
 * @method void __construct(string $servername, string $username, string $password, string $dbname)
 */
class DBDriver {
    private $servername;
    private $username;
    private $password;
    private $dbname;
    private $conn;
    /**
     * DBDriver constructor.
     * @param string $servername
     * @param string $username
     * @param string $password
     * @param string $dbname
     * 
     */
    public function __construct(string $servername, string $username, string $password, string $dbname) {
        $this->servername = $servername;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
    }

    /**
     * Connect to the database
     * @throws Exception if the connection fails
     * @return void
     */
    public function connect() {
        $this->conn = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);
        if (!$this->conn) {
            throw new Exception("". mysqli_connect_error());
        }
    }
    /**
     * Execute a query on the database
     * @param string $sql
     * @param mixed ...$params
     * @throws Exception if the query fails
     * @return mysqli_result|bool
     */
    public function query(string $sql, ...$params): mysqli_result|bool{
        if (!$this->conn) {
            throw new Exception("Currently not connected to a database");
        }
        $stmt = $this->conn->prepare($sql);
        $success = $stmt->execute($params);
        if (!$success) {
            throw new Exception("". mysqli_error($this->conn));
        }
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    /**
     * Close the connection to the database
     * @return void
     */
    public function close_connection() {
        mysqli_close($this->conn);
    }
    
    /**
     * Get the id of the last inserted row
     * @return int
     */
    public function get_last_inserted_id(): int {
        return mysqli_insert_id($this->conn);
    }
}
?>