<?php
namespace pLinq;
class Having {
    public $Other;
    public $Conditions = array();
    public function select($fields): Select
    {
        return new Select($this, $fields);
    }
    public function limit($numberOfRows, $offset = 0): Limit
    {
        $limit = new Limit();
        $limit->Offset = $offset;
        $limit->Rows = $numberOfRows;
        $limit->Other = $this;
        return $limit;
    }
    public function orderBy($fields): Order
    {
        $order = new Order();
        $order->Fields = $fields;
        $order->Other= $this;
        return $order;
    }
}
