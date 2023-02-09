<?php

use pLinq\sqlicon;
use PHPUnit\Framework\TestCase;

require('../sqlicon.php'); # only thing needed for texting here.

class mysqlwrapperTest extends TestCase
{
    /**
     * @var sqlicon
     */
    private $mysqlWrapper;

    /**
     * Set up the sqlicon object before each test
     */
    protected function setUp(): void
    {
        $this->mysqlWrapper = new sqlicon("localhost", "root", "", "fake_database", 3306, null, true, "logfile.log", "exceptionfile.log");
    }

    /**
     * Test the connection to the database
     */
    public function testConnection(): void
    {
        $this->assertInstanceOf(sqlicon::class, $this->mysqlWrapper);
    }

    /**
     * Test the query function
     */
    public function testQuery(): void
    {
        $query = "SELECT `TABLE_SCHEMA` 
                  FROM `information_schema`.`tables`
                  LIMIT 1";

        $result = $this->mysqlWrapper->query($query);
        $this->assertInstanceOf(mysqli_result::class, $result);
    }

    /**
     * Test the real_escape_string function
     */
    public function testRealEscapeString(): void
    {
        $string = "Hello ' World";
        $escapedString = $this->mysqlWrapper->real_escape_string($string);
        $this->assertEquals("Hello \\' World", $escapedString);
    }

    /**
     * Test logging an exception
     */
    /*public function testLogException(): void
    {
        $exception = "Exception message";
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('');
        $this->mysqlWrapper->logException($exception);
    }*/
}
