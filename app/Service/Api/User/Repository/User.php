<?php 

namespace App\Service\Api\User\Repository;
use  App\Dbhandler\Database ; 

class User
{
    private $db;
    private $data;

    public function __construct() {
         $this->db = (new Database)->setDB();
    }

    public function findById($id){
        $sql = "SELECT unique_id, username, email,
                                    phone, address_building, address_city, 
                                    address_state, address_zipcode, joined
                    FROM users
                    WHERE unique_id = :unique_id ";

        $params = ["unique_id"=>$id];

        $result = ($this->db->query($sql, $params))->results();
        return $result ? $result[0] : [] ;
    }


    public function isUserAvailable($id){
        $user = $this->findById($id);
        return !empty($user) ? true : false ;
    }


    public function format($userData) {
        extract($userData);
        return [
                "unique_id"=> "$unique_id",
                "username"=> "$username",
                "email"=> "$email",
                "phone"=> "$phone",
                "address"=> [
                  "building" => "$address_building",
                  "city" => "$address_city",
                  "state" => "$address_state",
                    "zip" => "$address_zipcode",
                  ],
                  "joined" => "$joined"
                ];
    }

}
