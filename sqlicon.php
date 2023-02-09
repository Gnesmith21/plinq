<?php
declare(strict_types=1);

namespace pLinq;

use Exception;
use mysqli;
use mysqli_result;

class sqlicon extends dbconnbase implements dbconninf
{
    private $connection;
    private $debug;
    private $mysqli_result = null;

    /**
     * @throws Exception
     */
    public function __construct(string $host, string $username, string $password, string $db,
                                int    $port = 3306, int $socket = null, bool $debug = false,
                                string $logFile = null, string $exceptionLogFile = null)
    {
        // Check to ensure the SQL db extension is enabled.
        if (!extension_loaded('mysqli')) {
            die("You must have the MYSQLI Extension Enabled.");
        }

        //creates connection and allows for raw socket connection.
        $this->connection = new mysqli($host, $username, $password, $db, $port, $socket);
        if ($this->connection->connect_error) {
            $this->logException('Failed to connect to database: ' . $this->connection->connect_error);
        }
        $this->debug = $debug;
        $this->logFile = $logFile;
        $this->exceptionLogFile = $exceptionLogFile;
    }

    /** queries for SQL data allows for debug mode and logs all interactions to file
     * debug mode will log query execution time and the query being executed.
     * @param string $query
     * @return bool|mysqli_result
     * @throws Exception
     */
    public function query(string $query)
    {
        if ($this->debug) {
            $this->logQuery("Query Being Executed: " . $query);
            $start_time = microtime(true);
            $this->logQuery("======================================");
        }

        $result = $this->connection->query($query);

        # check the execution time if in debug mode
        if ($this->debug) {
            $end_time = microtime(true);
            $execution_time = $end_time - $start_time;

            $this->logQuery("- Query execution time: $execution_time seconds\n");
            $this->logQuery("======================================");
        }

        if (!$result) {
            $this->logException('Failed to execute query: ' . $this->connection->error);
        }

        $this->mysqli_result = $result;
        return  $this->mysqli_result;
    }

    /**
     * Cleans inputs for SQL query
     * @param string $inputs
     * @return string
     */
    public function escape(string $inputs) : string
    {
        return $this->connection->real_escape_string($inputs);
    }

    /**
     * returns the associative array
     * @return array
     */
    public function returnArray() : array
    {
        return mysqli_fetch_all($this->mysqli_result, MYSQLI_ASSOC);
    }
}