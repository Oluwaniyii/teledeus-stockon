<?php 

namespace App\Service\User\Repository;
use  App\Dbhandler\Database ; 

class UserAuthToken
{
    private $db;
    private $table = "auth_token";
    private $data;

    public function __construct() {
         $this->db = (new Database)->setDB();
    }

    public function findCookies($userEmail){
        $sql = "SELECT * 
                     FROM `$this->table`
                     WHERE email = :email
                     AND is_expired = 0";

        $params = ["email"=>$userEmail];
        return ($this->db->query($sql, $params))->first();
    }

}

