<?php
namespace pLinq;
class Join {
    public $LeftAs;
    public $RightAs;
    public $JoinFields;
    public $JoinAlias;
    public function where($conditions): Where
    {
        $where = new Where();
        $where->Other = $this;
        $where->Conditions = $conditions;
        return $where;
    }
    public function join($toJoin, $joinFields, $joinAlias = null): Join
    {
        $join = new Join();
        $join->LeftAs = $this;
        $join->RightAs = $toJoin;
        $join->JoinFields = $joinFields;
        $join->JoinAlias = $joinAlias;
        return $join;
    }
    public function groupBy($fields): Group
    {
        $group = new Group();
        $group->Other = $this;
        $group->Fields = $fields;
        return $group;
    }
    public function orderBy($fields): Order
    {
        $order = new Order();
        $order->Fields = $fields;
        $order->Other = $this;
        return $order;
    }
    public function select($fields): Select
    {
        return new Select($this, $fields);
    }
}
