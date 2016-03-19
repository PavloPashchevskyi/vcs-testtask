<?php

/**
 * Description of DBConnection
 *
 * @author ppd
 */
class DBConnection 
{
    protected $connection;
    public function __construct() 
    {
        global $dbConnectionOptions;
        $dsn = $dbConnectionOptions['server'].
                ':host='.$dbConnectionOptions['host'];
        if(!empty($dbConnectionOptions['port'])) {
            $dsn.=':'.$dbConnectionOptions['port'];
        }
        $dsn.=';dbname='.
                $dbConnectionOptions['dbname'];
        $username = $dbConnectionOptions['user'];
        $password = $dbConnectionOptions['password'];
        $options[PDO::ATTR_PERSISTENT] = $dbConnectionOptions['persistent'];
        try {
            $this->connection = new PDO($dsn, $username, $password, $options);
        }
        catch (PDOException $e) {
            echo $e->getCode()." ".$e->getMessage()."<br>";
        }
    }
    
    public function __destruct() {
        $this->connection = null;
    }
}
