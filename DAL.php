<?php

declare(strict_types=1);

namespace pLinq;

use Exception;
use PDO;

/**
 * DAL class for database access
 */
class DAL
{
    // Array to store database connections
    private $connections = [];

    public function __construct()
    {
        // check if the php version is greater than 7.1
        if (version_compare(phpversion(), '7.1.0', '<')) {
            die('This script requires at least PHP version 7.1.0. Your version is ' . phpversion());
        }
    }

    /**
     * Adds a connection to the connections array to allow for multiple stored connections.
     *
     * @param string $dbType
     * @param string $name - Name of the Database connection
     * @param string $server - Name of the server to connect to
     * @param string $username - User for the database connection
     * @param string $password - Password for the database connection
     * @param string $dbName - Name of the database
     * @param bool $autoGenerate - Flag to enable auto generation of classes based on table names.
     * @param null $socket
     * @return void
     * @throws Exception
     */
    public function addConnection(string $name,
                                  string $server,
                                  string $username,
                                  string $password,
                                  string $dbName,
                                  bool   $autoGenerate = false,
                                  string $dbType = "mysqli",
                                         $socket = null): void
    {

        if ($dbType == "mysqli") {
            // add the connection to the current instance of the connection array.
            $this->connections[$name] = new sqlicon($server, $username, $password, $dbName);
        } else {
            $this->connections[$name] = new sqlcon($dbType, $server, $dbName, $username, $password);
        }

        // check if autogenerate is set to true.
        if ($autoGenerate) {

            // scrub the input data before we add it to the query.
            $dbName = $this->connections[$name]->escape($dbName);

            // get the SQL needed for the type of DB
            $sql = $this->getTableSchemaFromType($dbType, $dbName);

            #run the query
            $this->connections[$name]->query($sql);

            #get the array (FetchAll)
            $tableNames = $this->connections[$name]->returnArray();

            #loop and build the classes
            foreach ($tableNames as $tableName) {
                $this->generateClass($name, $tableName['table_name']);
            }
        }

    }

    /**
     * Retrieves the connection instance from the specified class.
     *
     * @param $className - class name for retrieving the connection
     * @return mixed
     */
    public function getConnection($className)
    {
        $connectionName = explode('\\', $className)[0];
        return $this->connections[$connectionName];
    }

    /**
     * Generates a class from connection info.
     * @param $connectionName - name of the connection used for the namespace
     * @param $name - Name of the table
     * @return void
     */
    public function generateClass($connectionName, $name)
    {


        $class = "namespace $connectionName; class $name extends \pLinq\DataModel {}";
        eval($class);
    }

    /**
     * Returns the SQL statement needed to retrieve the table schema
     * @param string $type
     * @param string $dbName
     * @return string
     * @throws Exception
     */
    private function getTableSchemaFromType(string $type, string $dbName){
        if($type == "mysqli" || $type == "mysql"){
            $sql = "SELECT `table_name` 
                    FROM `information_schema`.`tables` 
                    WHERE `table_schema` = '$dbName'";
        }elseif ($type == "pgsql"){
            $sql = "SELECT table_name
                    FROM information_schema.tables
                    WHERE table_catalog = '$dbName'
                    AND table_schema = 'public';";
        }elseif ($type == "mssql"){
            $sql = "SELECT table_name
                    FROM information_schema.tables
                    WHERE table_catalog = '$dbName'
                    AND table_schema = 'dbo';";
        }else{
            throw new exception(" DB Not Supported.");
        }
        return $sql;
    }

    /**
     * Create a map between the table and the connection.
     * @param string $tableName - name of the database table
     * @param string $nameOfConnection - name of the database connection instance
     * @return void
     * @throws Exception
     */
    public function mapToConnection(string $tableName, string $nameOfConnection)
    {
        if (!array_key_exists($nameOfConnection, $this->connections)) {
            throw new Exception("Error: No database connection found with the name '$nameOfConnection'");
        }

        $db = $this->connections[$nameOfConnection];

        // Get the driver name


        // Check if the connection is a PDO instance
        if ($db instanceof sqlcon) {

            $driver = $db->getDBType();
            // Escaping the table name to prevent SQL injection attacks
            $tableName = $db->escape($tableName);

            // Construct the SQL query based on the database driver
            if ($driver === 'mysql') {
                $query = "SELECT `TABLE_SCHEMA` 
                      FROM `information_schema`.`tables`  
                      WHERE table_name = $tableName 
                      LIMIT 1";
            } elseif ($driver === 'pgsql') {
                $query = "SELECT table_schema 
                      FROM information_schema.tables 
                      WHERE table_name = $tableName 
                      AND table_catalog = '$dbName' 
                      AND table_schema = 'public' 
                      LIMIT 1";
            } elseif ($driver === 'sqlsrv') {
                $query = "SELECT table_schema 
                      FROM information_schema.tables 
                      WHERE table_name = $tableName 
                      AND table_catalog = '$dbName' 
                      AND table_schema = 'dbo' 
                      LIMIT 1";
            } else {
                throw new Exception("Error: Unsupported database driver '$driver'");
            }
            $tableName = str_replace("'", "", $tableName);
            $this->generateClass($nameOfConnection, $tableName);

          //  if ($result) {
                // Create a class for the table.

          //  } else {
         //       throw new Exception("Error: No table found in the database for '$tableName'");
         //   }
        } elseif ($db instanceof sqlicon) {
            $this->generateClass($nameOfConnection, $tableName);
        } else {
            throw new Exception("Error: DB Driver Issues");
        }
    }
}