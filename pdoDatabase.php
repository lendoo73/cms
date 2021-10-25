<?php
class UsePDO {
    // properties:
    private $servername;
    private $username;
    private $password;
    private $dbname;
    private $conn;
    public $create;
    public $status;
    
    function __construct($servername, $username, $password, $dbname = false, $create = false) {
        $this->servername = $servername;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->create = $create;

        $this->status = new stdClass();
        $this->status->success = null;
        $this->status->code = null;
        $this->status->name = null;
        $this->status->description = null;
        $this->status->error = null;
        $this->status->data = null;
        $this->connect();
    }
    
    function __destruct() {
        // The connection will be closed automatically when the script ends, this close just for secure...
        $this->conn = null;
    }
    
    function clearStatus() {
        $this->status->code = null;
        $this->status->name = null;
        $this->status->description = null;
        $this->status->error = null;
        $this->status->data = null;
    }

    function fillSuccess() {
        $this->status->code = 200;
        $this->status->name = "ok";
        $this->status->description = "success";
    }
    
    function checkPDO() {
        if (!defined("PDO::ATTR_DRIVER_NAME")) {
            return false;
        } else {
            return true;
        }
    }
    
    function connect() {
        $this->clearStatus();
        // check PDO:
        if (!($this->checkPDO())) {
            return $this->status->error = "PDO not available";
        }
        
        try {
            $this->conn = new PDO(
                "mysql:host=$this->servername;dbname=$this->dbname", 
                $this->username, 
                $this->password
            );
            // set the PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->status->success = "Connected successfully.";
        } catch(PDOException $err) {
            if (strpos($err->getMessage(), "Unknown database") && $this->create) {
                $this->createDatabase($this->dbname);
            } else $this->status->error = "Connection failed: " . $err->getMessage();
        }
    }
    
    function createDatabase($name) {
        $this->clearStatus();
        try {
            $this->conn = new PDO("mysql:host=$this->servername", $this->username, $this->password);
            // set the PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "CREATE DATABASE $name";
            // use exec() because no results are returned
            $this->conn->exec($sql);
            $this->status->success = "Database created successfully.";
        } catch(PDOException $err) {
            $this->status->error = $sql . "<br>" . $err->getMessage();
        }
        $conn = null;
    }
    
    function execQuery($sql, $successMessage) {
        $this->clearStatus();
        try {
            // use exec() because no results are returned
            $this->conn->exec($sql);
            $this->status->success = $successMessage;
        } catch(PDOException $err) {
            $this->status->error = $sql . "<br>" . $err->getMessage();
        }
    }
    
    function createTable($sql) {
        $this->execQuery($sql, "Table created successfully.");
    }
    
    function insert($sql) {
        $this->execQuery($sql, "New record created successfully");
    }
    
    function selectData($sql) {
        $this->clearStatus();
        try {   
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $this->status->success = $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $this->fillSuccess();
            $this->status->data = $stmt->fetchAll();
        } catch(PDOException $err) {
            $this->status->error = "Error: " . $err->getMessage();
        }
        return $this->status->data;
    }
    
    function update($sql) {
        $this->clearStatus();
        try {   
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $this->status->success = $stmt->rowCount() . " records UPDATED successfully.";
            $this->fillSuccess();
        } catch(PDOException $err) {
            $this->status->error = "Error: " . $err->getMessage();
        }
    }

    function delete($sql) {
        $this->execQuery($sql, "Row(s) deleted successfully.");
    }
    
    function isUniqueData($table, $column, $value) {
        $sql = "SELECT $column 
                FROM $table
                WHERE $column='$value'
        ";
        $this->selectData($sql);
        return (!(count($this->status->data))) ? true : false;
    }
    
}
?>