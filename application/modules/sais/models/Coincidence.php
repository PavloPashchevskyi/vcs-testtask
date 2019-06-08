<?php

namespace Application\Modules\Sais\Models;

use Application\Core\Model;

class Coincidence extends Model
{
    protected $tableName = 'tCoincidences';

    public function selectConclusionConditions($conclusionName)
    {
        $sql = 'SELECT tConclusions.conclusion_name, tConditions.condition_name, '.
                $this->tableName.'.presence FROM '.$this->tableName.' '.
                'JOIN tConclusions ON('.$this->tableName.'.ConclusionID=tConclusions.conclusion_id) '.
                'JOIN tConditions ON ('.$this->tableName.'.ConditionID=tConditions.condition_id) '.
                'WHERE tConclusions.conclusion_name=:conclusion_name '.
                'ORDER BY tConditions.condition_name ';
        $query = $this->connection->prepare($sql);
        if(!$query) {
            exit("Unable to execute the query ".$sql."<br>");
        }
        $query->bindValue('conclusion_name', $conclusionName, \PDO::PARAM_STR);
        $query->execute();
        $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        return $results;
    }

    public function selectConclusionConditionsQuantity()
    {
        $sql = 'SELECT tConclusions.conclusion_name, COUNT('.$this->tableName.'.conditionid) AS conditions_count '.
            'FROM '.$this->tableName.' '.
            'JOIN tConclusions ON('.$this->tableName.'.conclusionid = tConclusions.conclusion_id) '.
            'GROUP BY tConclusions.conclusion_id';
        $query = $this->connection->query($sql);
        if(!$query) {
            exit("Unable to execute the query ".$sql."<br>");
        }
        $results = $query->fetchAll(\PDO::FETCH_ASSOC);

        return $results;
    }

    public function selectCoincidencesRelated($orderBy = [])
    {
        if(!empty($orderBy)) {
            $orderByClause = 'ORDER BY ';
        }
        else {
            $orderByClause = '';
        }
        $i = 0;
        foreach($orderBy as $field => $order) {
            $orderByClause.=$field.' '.$order;
            if($i<count($orderBy)-1) $orderByClause.=', ';
            $i++;
        }
        $sql = 'SELECT tCoincidences.coincidence_id, '.
                'tConclusions.conclusion_id, '.
                'tConclusions.conclusion_name, '.
                'tConditions.condition_id, '.
                'tConditions.condition_name, '.
                'tCoincidences.presence '.
                'FROM tCoincidences '.
                'JOIN tConclusions ON(tCoincidences.ConclusionID=tConclusions.conclusion_id) '.
                'JOIN tConditions ON(tCoincidences.ConditionID=tConditions.condition_id) '.
                $orderByClause;
        $query = $this->connection->query($sql);
        if(!$query) {
            exit("Unable to execute the query ".$sql."<br>");
        }
        $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        return $results;
    }
}

