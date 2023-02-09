<?php
declare(strict_types=1);

namespace pLinq;

use PDO;
use PDOException;

class sqlcon extends dbconnbase implements dbconninf
{
    private $conn;
    private $stmt = null;
    private $debug = false;

    public function __construct($driver, $host, $dbname, $username, $password, $socket = null) {
        try {
            if ($socket) {
                $dsn = "$driver:unix_socket=$socket;dbname=$dbname";
            } else {
                $dsn = "$driver:host=$host;dbname=$dbname";
            }
            $this->conn = new PDO($dsn, $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->logException('Failed to connect to database: ' . $e->getMessage());
        }
    }

    public function getDBType(){
        return $this->conn->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    public function query(string $sql) {
        try {
            if($this->debug) {
                $start = microtime(true);
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            if($this->debug) {
                $end = microtime(true);
                $this->logQuery("=================PDO Wrapper=====================\n");
                $this->logQuery($sql);
                $this->logQuery($end - $start);
                $this->logQuery("=======================End========================\n");
            }
            $this->stmt = $stmt;
            return $this->stmt;
        } catch (PDOException $e) {
            $this->logException('Failed to connect to database: ' . $e->getMessage());
        }
        return false;
    }

    public function returnArray()
    {
        try {
            if (!empty($this->stmt)) {
                return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
            }else{
                return false;
           }
        } catch (PDOException $e) {
            $this->logException("Error fetching all rows: " . $e->getMessage());
        }
        return false;
    }

    public function returnOneArray() {
        try {
            if (!empty($this->stmt)) {
                return $this->stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                return false;
            }
        } catch (PDOException $e) {
            $this->logException("Error fetching one rows: " . $e->getMessage());
        }
        return false;
    }

    public function escape(string $value) {
        return $this->conn->quote($value);
    }

    public function close() {
        $this->conn = null;
    }
}