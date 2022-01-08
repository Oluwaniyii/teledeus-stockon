<?php 

namespace App\Service\_Developer\Auth\Repository;


use  App\Dbhandler\Database ; //will be injected

class AccountRepository
{
    private $db;
    private $data;

    public function __construct() {
        $this->db = (new Database)->setDB();
        
        if(!$this->checkTable())
            $this->createTable();

    }

    public function add($user){
        $fields = [
            "unique_id",
            "firstname",
            "lastname",
            "email",
            "password",
         ];
        
        $insert = $this->db->insert("accounts", $fields, $user) ;
        
        return !$insert->error() ? true: false ;

    }

    public function findByEmail($email){
        $sql = "SELECT * 
                    FROM accounts
                    WHERE email = :email";

       $params = ["email"=>$email];

        return ($this->db->query($sql, $params))->first();
    }

    public function findById($acc_id){
        $sql = "SELECT unique_id, firstname, lastname, email, meta
                    FROM accounts
                    WHERE unique_id = :unique_id";

       $params = ["unique_id"=>$acc_id];

       $result = ($this->db->query($sql, $params))->results() ;
        return  empty($result) ? [] : $result[0];
    }

    public function update($id, $data){
        $validData = [];
        foreach ($data as $key => $value){
            if($value) $validData[$key] = $value;
        }

        $update =  $this->db->update("accounts", $id, $validData);
        return !$update->error() ? true: false ;
    }

    private function checkTable(){
        $sql =  "SELECT 1
        FROM accounts";

        $res = ($this->db->query($sql))->results();
        return $res ? true : false;
    }

    private function createTable(){
        $sql = "CREATE TABLE `accounts` (
            `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `unique_id` varchar(30) NOT NULL,
            `firstname` varchar(30) NOT NULL,
            `lastname` varchar(30) NOT NULL,
            `email` varchar(60) NOT NULL,
            `password` text NOT NULL,
            `meta` timestamp NOT NULL DEFAULT current_timestamp()
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        $res = ($this->db->query($sql))->results();
        return $res ? true : false;
    }
}
