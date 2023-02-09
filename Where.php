<?php
namespace pLinq;
use pLinq\Limit;
use pLinq\Order;
use pLinq\Select;

class Where
{
public $Other;
public $Conditions;

public function select($fields): Select
{
return new Select($this, $fields);
}

public function groupBy($fields): Group
{
return new Group($this, $fields);
}

public function limit($numberOfRows, $offset = 0): Limit
{
return new Limit($this, $numberOfRows, $offset);
}

public function orderBy($fields): Order
{
return new Order($this, $fields);
}

public function update($data)
{
$class = Select::getFirstTable($this);
global $dal;
$db = $dal->getConnection($class);

$fields = array_map(function ($key, $value) {
return "`$key` = '$value'";
}, array_keys($data), $data);

$sql = "UPDATE `$class` SET " . implode(',', $fields) . Select::getSqlFragment($this, true);

$db->query($sql);
}
}