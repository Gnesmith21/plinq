<?php
namespace pLinq;
class Limit {
    public $Other;
    public $Rows;
    public $Offset;
    public function select($fields): Select
    {
        return new Select($this, $fields);
    }
}