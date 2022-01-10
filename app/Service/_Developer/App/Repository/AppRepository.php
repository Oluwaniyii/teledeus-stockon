<?php 

declare(strict_types=1);

namespace App\Service\_Developer\App\Repository;

use  App\Dbhandler\Database ;

class AppRepository
{
    private $db;
    private const TABLE_NAME = "apps";

    public function __construct() {
        $this->db = (new Database)->setDB();
      
        if(!$this->checkTable())
            $this->createTable();
        
    }

    public function add($app){
        $fields = [
            "unique_id",
            "account_id",
            "client_id",
            "client_secret",
            "app_name",
            "app_description",
            "app_type",
            "error_redirect_url",
            "success_redirect_url",
         ];
        
        $insert = $this->db->insert("apps", $fields, $app) ;
        return !$insert->error() ? true: false ;
    }


    public function findApps($accountID){
        $sql =  "SELECT unique_id, account_id, app_name, app_description, created
                     FROM apps
                     WHERE account_id=:account_id";

        $params = ["account_id"=>$accountID];

         return ($this->db->query($sql, $params))->results();
    }


    public function findAppById($appID, $safe=false){
        if($safe)
            $sql =  "SELECT unique_id, account_id, app_name,
                                        app_description, created
                        FROM apps
                        WHERE unique_id=:unique_id";
        else
            $sql =  "SELECT *
                            FROM apps
                            WHERE unique_id=:unique_id";

        $params = ["unique_id"=>$appID];

        $result = ($this->db->query($sql, $params))->results();
        return  empty($result) ? [] : $result[0];
    }

    public function findAppWithClientId($clientID, $safe=false){
        if ($safe) {
            $sql =  "SELECT unique_id, client_id, account_id, app_name,
            app_description, created
            FROM apps
           WHERE client_id=:client_id ";
        }
        else {
            $sql =  "SELECT *
            FROM apps
           WHERE client_id=:client_id ";
        }
        
        $params = ["client_id"=>$clientID];

        $result = ($this->db->query($sql, $params))->results();
        return  empty($result) ? [] : $result[0];
    }

    public function findAppWithCredentials($clientID, $clientSecret){
        $sql =  "SELECT *
                     FROM apps
                    WHERE client_id=:client_id
                    AND client_secret=:client_secret";

        $params["client_id"] = $clientID;
        $params["client_secret"]= $clientSecret;

        $result = ($this->db->query($sql, $params))->results();
        return  empty($result) ? [] : $result[0];
    }


    public function findUsersConnectedToApp($clientID){
        $sql =  "SELECT DISTINCT user_identity
                    FROM access_token
                    WHERE client_id = :client_id
                    AND is_expired=0;";

        $params["client_id"] = $clientID;

        $result = ($this->db->query($sql, $params))->results();
        return  empty($result) ? [] : $result[0];
    }


    public function update($appID, $data){

        $validData = [];
        foreach ($data as $key => $value){
            if($value) $validData[$key] = $value;
        }

        $update =  $this->db->update("apps", $appID, $validData);
        return !$update->error() ? true: false ;
    }

    public function remove($id){
        $action = $this->db->delete('apps', $id);
        return !$action->error() ? true : false ;
    }

    private function checkTable(){
        $sql =  "SELECT 1
        FROM apps";

        $res = ($this->db->query($sql))->results();
        return $res ? true : false;
    }

    private function createTable(){
        $sql = "CREATE TABLE `apps` (
            `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `unique_id` varchar(40) NOT NULL,
            `account_id` varchar(150) NOT NULL,
            `client_id` text NOT NULL,
            `app_type` varchar(30) NOT NULL,
            `client_secret` text NOT NULL,
            `app_name` varchar(300) NOT NULL,
            `app_description` text NOT NULL,
            `success_redirect_url` varchar(150) NOT NULL,
            `error_redirect_url` varchar(150) NOT NULL,
            `created` timestamp NOT NULL DEFAULT current_timestamp()
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        $res = ($this->db->query($sql))->results();
        return $res ? true : false;
    }
}