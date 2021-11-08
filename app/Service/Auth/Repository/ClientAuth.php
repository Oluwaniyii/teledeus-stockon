<?php 

namespace App\Service\Auth\Repository;
use  App\Dbhandler\Database ; //will be injected

class ClientAuth
{
    private $db;
    private $data;

    public function __construct() {
          $this->db = (new Database)->setDB(__DIR__ . '/../../../../config/db2.php');
    }

    public function getAppByCredentials($client_id, $client_secret){
     $sql = "SELECT app_name, account_id, client_id, client_secret 
                 FROM apps where client_id=:client_id AND client_secret = :client_secret ";

        $params["client_id"] = $client_id;
        $params["client_secret"] = $client_secret;

        return ($this->db->query($sql, $params))->first();
    }

}
