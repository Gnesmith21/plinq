<?php
namespace pLinq;
use Exception;

class dbconnbase {

    public $logFile;
    public $exceptionLogFile;

    /**
     * Logs the query string to the specified log files
     * @param $query
     * @return void
     * @throws Exception
     */
    public function logQuery($query): void
    {
        if (!$this->logFile) {
            return;
        }
        $file = fopen($this->logFile, 'a');
        if (!$file) {

            throw new Exception('Unable to open log file: ' . $this->logFile);
        }
        fwrite($file, $query . "\n");
        fclose($file);
    }

    /** Log to file for exceptions in queries
     * @param $exception
     * @return void
     * @throws Exception
     */
    public function logException($exception) : void
    {
        if (!$this->exceptionLogFile) {
            return;
        }
        $file = fopen($this->exceptionLogFile, 'a');
        if (!$file) {
            throw new Exception('Unable to open exception log file: ' . $this->exceptionLogFile);
        }
        fwrite($file, $exception . "\n");
        fclose($file);
    }
}