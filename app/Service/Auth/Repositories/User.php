<?php 

namespace App\Service\Auth\Repositories;
use  App\Dbhandler\Database ; 

class User
{
    private $db;
    private $data;

    public function __construct() {
         $this->db = (new Database)->setDB();
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
                                    address_zipcode, joined
                    FROM users
                    WHERE unique_id = :unique_id ";

        $params = ["unique_id"=>$id];

        return ($this->db->query($sql, $params))->results();
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

}
