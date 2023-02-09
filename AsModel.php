<?php
namespace pLinq;

class AsModel {


    public $Table;
    public $Alias;
    public $Other;
    public function join($other, $fields, $joinAlias = null): Join
    {
        $join = new Join();
        $join->RightAs = $other;
        $join->LeftAs = $this;
        $join->JoinAlias = $joinAlias;
        $join->JoinFields = $fields;
        return $join;
    }
    public function where($conditions): Where
    {
        $where = new Where();
        $where->Other = $this;
        $where->Conditions = $conditions;
        return $where;
    }
    public function select($fields): Select
    {
        return new Select($this, $fields);
    }
}