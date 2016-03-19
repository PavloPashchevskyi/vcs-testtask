<?php
    class Coincidence extends Model
    {
        protected $tableName = 'tcoincidences';
        
        public function selectConclusionConditions($conclusionName)
        {
            $sql = 'SELECT tconclusions.conclusion_name, tconditions.condition_name, '.
                    $this->tableName.'.presence FROM '.$this->tableName.' '.
                    'JOIN tconclusions ON('.$this->tableName.'.ConclusionID=tconclusions.conclusion_id) '.
                    'JOIN tconditions ON ('.$this->tableName.'.ConditionID=tconditions.condition_id) '.
                    'WHERE tconclusions.conclusion_name="'.$conclusionName.'" '.
                    'ORDER BY tconditions.condition_name ';
                $query = $this->connection->query($sql);
                if(!$query) {
                    exit("Unable to execute the query ".$sql."<br>");
                }
                $results = $query->fetchAll(PDO::FETCH_ASSOC);
                return $results;
        }
        
        public function selectCoincidencesRelated($orderBy)
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
            $sql = 'SELECT tcoincidences.coincidence_id, '.
                    'tconclusions.conclusion_id, '.
                    'tconclusions.conclusion_name, '.
                    'tconditions.condition_id, '.
                    'tconditions.condition_name, '.
                    'tcoincidences.presence '.
                    'FROM tcoincidences '.
                    'JOIN tconclusions ON(tcoincidences.ConclusionID=tconclusions.conclusion_id) '.
                    'JOIN tconditions ON(tcoincidences.ConditionID=tconditions.condition_id) '.
                    $orderByClause;
            $query = $this->connection->query($sql);
            if(!$query) {
                exit("Unable to execute the query ".$sql."<br>");
            }
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        }
    }
?>
