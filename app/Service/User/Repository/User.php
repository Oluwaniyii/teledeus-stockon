<?php 

namespace App\Service\User\Repository;
use  App\Dbhandler\Database ; 

class User
{
    private $db;
    private $data;

    public function __construct() {
        $this->db = (new Database)->setDB();

        if(!$this->checkTable())
            $this->createTable();
    }

    public function findAll(){
        $sql = "SELECT unique_id, username, email,
                                    phone, address_building, address_city,
                                    address_building, address_zipcode, joined
                                FROM users";

         return ($this->db->query($sql))->results();
    }

    public function findById($id){
        $sql = "SELECT unique_id, username, email,
                                    phone, address_building, address_city, address_building,
                                    address_state, address_zipcode, joined
                    FROM users
                    WHERE unique_id = :unique_id ";

        $params = ["unique_id"=>$id];

        $result = ($this->db->query($sql, $params))->results();

        return empty($result) ? [] : $result[0];
    }

    public function findByEmail($email){
        $sql = "SELECT unique_id, username, email,
                                    phone, address_building, address_city, address_building,
                                    address_zipcode, joined, password
                    FROM users
                    WHERE email = :email";

       $params = ["email"=>$email];

        return ($this->db->query($sql, $params))->first();
    }

    public function findByUsername($username){
        $sql = "SELECT unique_id, username, email,
                                    phone, address_building, address_city, address_building,
                                    address_zipcode, joined
                    FROM users
                    WHERE username = :username";

       $params = ["username"=>$username];

        return ($this->db->query($sql, $params))->first();
    }


    private function getUserObject($id) {
            $sql = "SELECT unique_id, username, email,
                                        phone, address_building, address_city, address_building,
                                        address_zipcode, joined
                        FROM users
                        WHERE unique_id = :unique_id";

        $params = ["unique_id"=>$id];

        return $this->db->query($sql, $params);
    }


    public function isUserAvailable($id)
    {
        # code...
        $userObject = $this->getUserObject($id);
        if($userObject->count() > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    // public function findOne(){};

    public function add($user){
        $fields = [
            "unique_id",
            "password",
            "username",
            "email",
            "phone",
            "address_building",
            "address_city",
            "address_state",
            "address_zipcode",
         ];
        
        $insert = $this->db->insert("users", $fields, $user) ;
        
        return !$insert->error() ? true: false ;

    }


    public function update($id, $data){
        $validData = [];
        foreach ($data as $key => $value){
            if($value) $validData[$key] = $value;
        }

        $update =  $this->db->update("users", $id, $validData);
        return !$update->error() ? true: false ;
    }


    public function drop($id){
        $action = $this->db->delete('users', $id);
        return !$action->error() ? true : false ;
     }


    public function formatUsers($data=[]) {
        if(empty($data)) {return []; }

        if(count($data) > 1) {
            $output = [];
            foreach ($data as $userData) { $output[] = $this->format($userData); };
            return $output ;
        } else  {
            return $this->format($data[0]);
        };
    }

    private function format($userData) {
        extract($userData);
        return [
                "unique_id"=> "$unique_id",
                "username"=> "$username",
                "email"=> "$email",
                "phone"=> "$phone",
                "address"=> [
                  "building" => "$address_building",
                  "city" => "$address_city",
                  "state" => "$address_building",
                    "zip" => "$address_zipcode",
                  ],
                  "joined" => "$joined"
                ];
    }

    private function checkTable(){
        $sql =  "SELECT 1
        FROM users";
  
        $res = ($this->db->query($sql))->results();
        return $res ? true : false;
    }
  
    private function createTable(){
        $sql = "CREATE TABLE `users` (
          `id` int(11) NOT NULL,
          `unique_id` varchar(100) NOT NULL,
          `username` varchar(30) NOT NULL,
          `email` varchar(100) NOT NULL,
          `phone` varchar(20) NOT NULL,
          `password` text NOT NULL,
          `address_building` varchar(255) DEFAULT NULL,
          `address_city` varchar(255) DEFAULT NULL,
          `address_state` varchar(255) DEFAULT NULL,
          `address_zipcode` varchar(20) DEFAULT NULL,
          `joined` datetime NOT NULL DEFAULT current_timestamp()
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
  
        $res = ($this->db->query($sql))->results();
        return $res ? true : false;
    }
  

}
