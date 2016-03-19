<?php

/**
 * Description of Condition
 *
 * @author ppd
 */
class Condition extends Model
{
    protected $tableName = 'tconditions';
    
    public function selectAbsent($conditionNames)
    {
        $strConditionNames = [];
        foreach($conditionNames as $i => $conditionName) {
            $strConditionNames[] = '"'.$conditionName.'"';
        }
        $conditionNamesSeparated = implode(',', $strConditionNames);
        $sql = 'SELECT * FROM '.$this->tableName.' WHERE '.$this->tableName.'.condition_name NOT IN('.
                $conditionNamesSeparated.')';
        $query = $this->connection->query($sql);
        if(!$query) {
            exit("Unable to execute the query ".$sql."<br>");
        }
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }
}
