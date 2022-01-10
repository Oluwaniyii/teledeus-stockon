<?php

declare(strict_types=1) ;

namespace  App\Dbhandler ;

use PDO;

class Database 
{
    protected $_dbh ;
    private $_query ;
    private $_count ;
    private $_error ;
    private $_logger ;
    private $_results ;

    public function setDB($configPath = null) {
        try {
            $config = require !is_null($configPath) ? $configPath : __DIR__ . '/../../config/db.php' ;
            $host = $config['host'];
            $dbname = $config['dbname'];
            $username = $config['user'];
            $password = $config['password'];
            $dsn = "mysql:host=$host;dbname=$dbname;";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];

            $this->_dbh = new PDO($dsn, $username, $password);
        }
            catch(PDOException $ex) {
                die($ex->getMessage);
        }

        return $this;
    }

    public function query($sql, $params = []){
        $this->_query = $this->_dbh->prepare($sql);
        // Bind Params
        foreach ($params as $key => $value) {
           $this->_query->bindValue(":$key", "$value");
        }

        // Execute
        try {
            $this->_query->execute();
            $this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
            $this->_count = $this->_query->rowCount();
        }catch (PDOException $ex) {
            $ex->getMessage();
            $this->_error = true;
        }
        
         return $this;
    }

    private function action($action, $table, $where = [])
	{
		if (count($where) === 3) {
			$operators = ["=", ">", "<", ">=", "<="];

			$field = $where[0];
			$operator = $where[1];
			$value = $where[2];

			if (in_array($operator, $operators)) {
				$sql = "{$action} FROM {$table} WHERE {$field} {$operator} ? ";

                echo "$sql";
                exit();
				if ($this->query($sql, [$value]) ) {
					return $this;
				}
			}
		}
        
		return false;
	}

	public function get($table, $where)
	{
		return $this->action("SELECT *", $table, $where);
	}

	public function delete($table, $id)
	{
        $sql = "DELETE FROM {$table} WHERE unique_id = :unique_id" ;
        $params = ["unique_id"=>$id];
        return $this->query($sql, $params);
	}

	public function insert($table, $fields = [], $data)
	{
		if (count($fields)) {
			$keys = array_keys($fields);
			$values = "";
			$index = 1;

			foreach ($fields as $field) {
				$values .= ":$field";
				if ($index < count($fields)) {
					$values .= ", ";
				}
				$index++;
			}

			$sql = "INSERT INTO {$table}  (`" .
			    	implode("`, `", $fields) .
                 "`) 
                 VALUES ({$values})";
                $this->query($sql, $data) ;
		}
      
		return $this;
	}

	public function update($table, $id, $fields)
	{
		$set = "";
		$index = 1;
		//set
		foreach ($fields as $field => $value) {
            if($value){
                $set .= " {$field} = :$field";
                if ($index < count($fields)) {
                    $set .= ", ";
                }
            }
			$index++;
		}

        $sql = "UPDATE {$table} SET{$set} WHERE unique_id=:unique_id";
        // Manually pass Id
        $fields['unique_id'] = "$id";

		if (!$this->query($sql, $fields)->error()) {
			return $this;
		}

		return false;
	}

    private function getParamType($value=null){
        switch($value) {
            case is_int($value):
              $type = PDO::PARAM_INT;
              break;
            case is_bool($value):
              $type = PDO::PARAM_BOOL;
              break;
            case is_null($value):
              $type = PDO::PARAM_NULL;
              break;
            default:
             $type = PDO::PARAM_STR;
        }

        echo "$type";

        return $type;
     }

    public function count(){return $this->_count;}
    public function results() {
        return $this->_results;
    } 
    public function first(){
     return count($this->_results) ? ($this->_results)[0] : [] ;
    }
    public function error() {return $this->_error;} 
}