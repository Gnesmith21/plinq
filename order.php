<?php
namespace pLinq;
class Order {
    public $fields;
    /**
     * @var Group
     */
    public $Other;
    public $Fields;

    public function select($fields): Select
    {
        return new Select($this, $fields);
    }
}
