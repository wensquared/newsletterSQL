<?php

namespace app\lib;

use app\lib\Interfaces\DatabaseInterface;
use Exception;
use PDO;
use PDOException;

class Mysql implements DatabaseInterface{

    protected $pdo = NULL; 
    protected $orderBy = [];


    public function __construct(array $config){
        try {
            $dsn = 'mysql:';
            $dsn .= 'host=';
            $dsn .= $config['host'] ?? '';
            $dsn .= ';dbname=';
            $dsn .= $config['dbname'] ?? '';
            $dsn .= ';port=';
            $dsn .= $config['port'] ?? 3306;
    
            $username= $config['username'] ?? '';
            $password= $config['password'] ?? '';
            
            $this->pdo = new PDO($dsn,$username,$password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getCon(){
        return $this->pdo;
    }


    public function delete(string $tablename, string $fieldvalue, string $field = 'id'){
        $sql=<<<SQL
        DELETE FROM $tablename WHERE $field=:fieldvalue;
SQL;

        $statement=$this->pdo->prepare($sql);
        $statement->bindParam(':fieldvalue',$fieldvalue,PDO::PARAM_STR);
        $statement->execute();
        return $statement->rowCount();
    }


    public function find(string $tablename,$fieldvalue, string $field='id'){
        $sql = <<<SQL
                SELECT * FROM $tablename WHERE $field=:id;
SQL;            
        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':id',$fieldvalue,PDO::PARAM_STR);
        $statement->execute();
        return $statement->fetchObject();
    }


    public function insert(string $tablename, array $fields){
        $strFields = implode(',',array_keys($fields));
        $valFields = ':'.implode(',:',array_keys($fields));

        $sql = <<<SQL
            INSERT INTO $tablename (  $strFields ) VALUES ($valFields);
SQL;

        $statement = $this->pdo->prepare($sql);
        foreach( $fields as $key=>$value){
            $statement->bindValue(":$key",$value,PDO::PARAM_STR);
        }
        if( $statement->execute() ){
            return $this->pdo->lastInsertId();
        }
        return false;
    }


    public function update(string $tablename, array $fields, $id, $field = 'id'){
        $sql = 'UPDATE '.$tablename.' SET ';

        $first = true;
        foreach ($fields as $key => $value) {
            (!$first) ? $sql .= ',' : $first = false;
            $sql .= $key.'=:'.$key;
        }

        $sql .= ' WHERE '.$field.' =:'.$field;

        $statement = $this->pdo->prepare($sql);

        foreach ($fields as $key => $value) {
            $statement->bindValue(":$key",$value,PDO::PARAM_STR);
        }
        $statement->bindValue(":$field",$id,PDO::PARAM_STR);
        return $statement->execute();

    }


    public function orderBy(string $field, $mode = 'asc'): object{
        if($mode != 'asc') $mode = 'desc';

        $this->orderBy[] = $field.' '.$mode;
        return $this;
    }


    public function getAll(string $tablename,$limit=''){
        $orderBy = '';
        if (!empty($this->orderBy)) {
            $orderBy = ' ORDER BY '.implode(',',$this->orderBy);
        }
        if($limit) $limit = ' LIMIT '.$limit;

        $sql = 'SELECT * FROM '.$tablename.$orderBy.$limit;
        $result = $this->pdo->query($sql);
        $this->orderBy = [];

        return $result->fetchAll(PDO::FETCH_CLASS);
    }
    
}
