<?php
namespace pLinq;

class Select {

    private $other;
    private $fields;
    private $executed = false;
    /**
     * @var Limit
     */
    private $Other;

    function __construct($other, $fields) {
        $this->other = $other;
        $this->fields = $fields;
    }

    public function firstOrDefault()
    {
        $limit = new Limit();
        $limit->Other = $this->other;
        $limit->Rows = 1;
        $limit->Offset = 0;
        $this->Other = $limit;
        $result = $this->toArray();
        return count($result) > 0 ? $result[0] : null;
    }

    private function getSql($select): string
    {
        $fields = is_array($select->fields) ? implode(',', $select->fields) : $select->fields;
        $tableName = is_string($select->other) ? explode('\\', $select->other)[1] : '';
        $table = $tableName ? " `$tableName` " : Select::getSqlFragment($select->other);

        return "SELECT $fields FROM $table";
    }

    function toArray() {
        $sql = $this->getSql($this);
        $table = Select::getFirstTable($this->other);
        global $dal;
        $db = $dal->getConnection($table);
        $db->query($sql);
        return $db->returnArray();
    }

    public static function getFirstTable($other) {
        if (is_string($other)) {
            return $other;
        }

        $class = get_class($other);
        switch ($class) {
            case 'pLinq\AsModel':
                return $other->Table;
            case 'pLinq\Join':
                return self::getFirstTable($other->LeftAs);
            case 'pLinq\Where':
            case 'pLinq\Group':
            case 'pLinq\Having':
            case 'pLinq\Limit':
            case 'pLinq\Order':
            case 'pLinq\Select':
                return self::getFirstTable($other->Other);
            default:
                break;
        }

        return null;
    }
    public static function getSqlFragment($other, $ignoreOther = false): string
    {
        $keyword = get_class($other);
        $sql = '';

        switch ($keyword) {
            case 'pLinq\AsModel':
                $tableName = explode('\\', $other->Table)[1];
                $sql .= "`$tableName` $other->Alias";
                $sql .= $other->Other ? self::getSqlFragment($other->Other) : '';
                break;
            case 'pLinq\Join':
                $rightType = get_class($other->RightAs);
                $sql .= self::getSqlFragment($other->LeftAs) . ' JOIN ';
                $sql .= ($rightType == 'AsModel') ? self::getSqlFragment($other->RightAs) . " ON $other->JoinFields" : "($innerSql) $other->JoinAlias ON $other->JoinFields";
                break;
            case 'pLinq\Where':
                $sql .= $other->Other && !$ignoreOther ? self::getSqlFragment($other->Other) : '';
                $sql .= " WHERE $other->Conditions";
                break;
            case 'pLinq\Group':
                $sql .= $other->Other ? self::getSqlFragment($other->Other) : '';
                $sql .= " GROUP BY $other->Fields";
                break;
            case 'pLinq\Having':
                $sql .= $other->Other ? self::getSqlFragment($other->Other) : '';
                $sql .= " HAVING $other->Conditions";
                break;
            case 'pLinq\Limit':
                $sql .= $other->Other ? self::getSqlFragment($other->Other) : '';
                $sql .= " LIMIT $other->Offset, $other->Rows";
                break;
            case 'pLinq\Order':
                $sql .= $other->Other ? self::getSqlFragment($other->Other) : '';
                $sql .= " ORDER BY $other->Fields";
                break;
        }

        return $sql;
    }
}