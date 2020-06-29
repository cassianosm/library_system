<?php
// MySQL DB Connection Class.
class DBConnection
{

    private $conn;
    private $user = 'library';
    private $pwd = 'library';
    private $server = 'localhost';
    private $dbname = 'library';

    public function __construct()
    {

        $this->conn = new mysqli($this->server, $this->user, $this->pwd, $this->dbname);

        if ($this->conn->connect_error) {
            die('Connection Error');
        }

    }

    public function getConnection()
    {
        return $this->conn;
    }

}

?>