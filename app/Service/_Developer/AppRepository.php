<?php 

declare(strict_types=1);

namespace App\Service\OAuth\Repository;

use  App\Dbhandler\Database ;

class AppRepository
{
    private $db;
    private $data;

    public function __construct() {
        $this->db = (new Database)->setDB(__DIR__ . '/../../../../config/db2.php');
    }

    public function add($app){
        $fields = [
            "unique_id",
            "account_id",
            "client_id",
            "client_secret",
            "app_name",
            "app_description",
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


    public function findAppById($appID){
        $sql =  "SELECT unique_id, account_id, app_name,
                                    app_description, created
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

}
