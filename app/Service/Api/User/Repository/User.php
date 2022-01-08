<?php 

namespace App\Service\Api\User\Repository;
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
