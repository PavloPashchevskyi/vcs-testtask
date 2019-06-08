<?php

namespace Application\Core;

/**
 * Description of Model
 *
 * @author ppd
 */
class Model extends DBConnection
{
    protected $tableName;
    protected $modelName;
    
    public function __construct() {
        parent::__construct();
        global $defaultOptions;
        $tableNamePrefix = $defaultOptions['dbtable']['prefix'];
        $tableNameSuffix = $defaultOptions['dbtable']['suffix'];
        $beginpos = strpos($this->tableName, (string) $tableNamePrefix)+strlen($tableNamePrefix);
        $endpos = strrpos($this->tableName, (string) $tableNameSuffix);
        $length = $endpos-$beginpos;
        $this->modelName = ucfirst(substr($this->tableName, $beginpos, $length));
    }
    
    /**
     * 
     * @param array $orderBy
     * @return array
     */
    public function findAll($orderBy = null)
    {
        $orderByClause = '';
        if(!empty($orderBy)) {
            $orderByClause.='ORDER BY ';
            $i = 0;
            foreach($orderBy as $field => $order) {
                $orderByClause.=$field.' '.$order;
                if($i<count($orderBy)-1) $orderByClause.=', ';
                $i++;
            }
        }
        $sql = 'SELECT * FROM '.$this->tableName.' '.$orderByClause;
        $query = $this->connection->query($sql);
        if(!$query) {
            exit("Unable to execute the query ".$sql."<br>");
        }
        $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        return $results;
    }
    
    /**
     * Returns one record from table by primary key
     *
     * @param int $id
     * @return array
     */
    public function findOne($id)
    {
        $primaryKeyName = strtolower($this->modelName).'_id';
        $sql = 'SELECT * FROM '.$this->tableName.' WHERE '.$primaryKeyName.'='.$id;
        $query = $this->connection->query($sql);
        if(!$query) {
            exit("Unable to execute the query ".$sql."<br>");
        }
        $result = $query->fetch(\PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * 
     * @param array $criteria
     * @param string $operation
     * @param array $orderBy
     * @return array
     */
    public function findBy($criteria, $operation = 'AND', $orderBy = null)
    {
        $whereClause = '';
        if(!empty($criteria)) {
            $whereClause .= 'WHERE ';
        }
        $i = 0;
        foreach($criteria as $key => $value) {
            $whereClause.=$key.'='.$value;
            if($i<count($criteria)-1) {
                $whereClause.=' '.$operation.' ';
            }
            $i++;
        }

        $orderByClause = '';
        if(!empty($orderBy)) {
            $orderByClause .= 'ORDER BY ';
        }
        $i = 0;
        foreach($orderBy as $field => $order) {
            $orderByClause.=$field.' '.$order;
            if($i<count($orderBy)-1) $orderByClause.=', ';
            $i++;
        }
        $sql = 'SELECT * FROM '.$this->tableName.' '.$whereClause.' '.$orderByClause;
        $query = $this->connection->query($sql);
        if(!$query) {
            exit("Unable to execute the query ".$sql."<br>");
        }
        $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        return $results;
    }

    /**
     * @param $what
     * @return bool
     */
    public function insert($what)
    {
        $fieldNames = array_keys($what);
        $fieldParams = array_map(function ($fieldName) {return ':'.$fieldName;}, $fieldNames);

        $preparedFieldNames = implode(',', $fieldNames);
        $preparedFieldParams = implode(',', $fieldParams);

        $sql = 'INSERT INTO '.$this->tableName.'('.$preparedFieldNames.') VALUES('.$preparedFieldParams.');';
        $query = $this->connection->prepare($sql);
        if($query->errorCode()) {
            exit('ERROR#'.$query->errorCode().': '.$query->errorInfo()."<br/>Unable to execute the query<br/>".$sql);
        }
        foreach ($what as $fieldName => $fieldValue) {
            $query->bindValue(':'.$fieldName, $this->unquoteString($fieldValue));
        }

        try {
            return $query->execute();
        } catch (\PDOException $exception) {
            exit(
                'EXCEPTION#'.$exception->getCode().': '.$exception->getMessage().
                "<br/>Unable to execute the query ".$sql.' with the following params '.$query->debugDumpParams()
            );
        }
    }
    
    /**
     * 
     * @param array $what
     * @param array $criteria
     * @param string $operation
     * @return boolean
     */
    public function update($what, $criteria, $operation = 'AND')
    {
        $whatClause = '';
        $i = 0;
        foreach($what as $key => $value) {
            $whatClause.=$key.'=:_'.$key;
            if($i<count($what)-1) {
                $whatClause.=', ';
            }
            $i++;
        }

        $whereClause = $this->sqlQueryWhereClause($criteria, $operation);

        $sql = 'UPDATE '.$this->tableName.' SET '.$whatClause.' WHERE '.$whereClause;
        $query = $this->connection->prepare($sql);
        if($query->errorCode()) {
            exit('ERROR#'.$query->errorCode().': '.$query->errorInfo()."<br/>Unable to execute the query<br/>".$sql);
        }
        foreach ($what as $key => $value) {
            $query->bindValue(':_'.$key, $this->unquoteString($value));
        }
        foreach ($criteria as $key => $value) {
            $query->bindValue(':'.$key, $this->unquoteString($value));
        }

        try {
            return $query->execute();
        } catch (\PDOException $exception) {
            exit(
                'EXCEPTION#'.$exception->getCode().': '.$exception->getMessage().
                "<br/>Unable to execute the query ".$sql.' with the following params '.$query->debugDumpParams()
            );
        }
    }
    
    /**
     * 
     * @param array $criteria
     * @param string $operation
     * @return boolean
     */
    public function delete($criteria, $operation = 'AND')
    {
        $whereClause = '';
        if(!empty($criteria)) {
            $whereClause = 'WHERE ';
        }

        $whereClause .= $this->sqlQueryWhereClause($criteria, $operation);
        $sql = 'DELETE FROM '.$this->tableName.' '.$whereClause;
        $query = $this->connection->prepare($sql);
        if($query->errorCode()) {
            exit('ERROR#'.$query->errorCode().': '.$query->errorInfo()."<br/>Unable to execute the query<br/>".$sql);
        }

        foreach ($criteria as $key => $fieldValue) {
            $query->bindValue(':'.$key, $this->unquoteString($fieldValue));
        }

        try {
            return $query->execute();
        } catch (\PDOException $exception) {
            exit(
                'EXCEPTION#'.$exception->getCode().': '.$exception->getMessage().
                "<br/>Unable to execute the query ".$sql.' with the following params '.$query->debugDumpParams()
            );
        }
    }
    
    /**
     * Returns next record id
     * 
     * @return int
     */
    public function calculateNextID()
    {
        $fieldName = strtolower($this->modelName).'_id';
        $sql = 'SELECT MAX('.$fieldName.') AS maxid FROM '.$this->tableName;
        $query = $this->connection->query($sql);
        if(!$query) {
            exit("Unable to execute the query ".$sql."<br>");
        }
        $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        $nextid = $results[0]['maxid']+1;
        return $nextid;
    }

    private function sqlQueryWhereClause($criteria, $operation = 'AND')
    {
        $whereClause = '';
        $i = 0;
        foreach($criteria as $key => $value) {
            $whereClause .= $key.'=:'.$key;
            if($i < count($criteria) - 1) {
                $whereClause .= ' ' . $operation . ' ';
            }
            $i++;
        }

        return $whereClause;
    }

    private function unquoteString($str)
    {
        return (is_string($str)) ? trim($str, "\"") : $str;
    }
}
