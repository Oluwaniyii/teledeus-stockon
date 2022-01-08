<?php 

declare(strict_types=1);

namespace App\Service\OAuth\Repository;

use  App\Dbhandler\Database ;

class OAuthRepository
{
    private $db;
    private $data;

    public function __construct() {
        $this->db = (new Database)->setDB();

        if(!$this->checkTable())
            $this->createTable();

    }

    public function saveTokenCode($tokenCode){
        $fields = [
            "unique_id",
            "code_string",
            "client_id",
            "client_redirect_url",
            "user_identity",
            "issued_at",
            "expiration_time"
         ];

         $insert = $this->db->insert("token_code", $fields, $tokenCode) ;

         return !$insert->error() ? true: false ;
    }

    public function getTokenCode($code){
        $sql =  "SELECT unique_id, code_string, client_id, user_identity,
                            client_redirect_url, issued_at, expiration_time
                    FROM token_code
                    WHERE code_string=:code_string
                    AND is_expired=0";

                    $params = ["code_string"=>$code];

                    $result = ($this->db->query($sql, $params))->results();
                    return  empty($result) ? [] : $result[0];
    }

    //unique_id
    public function removeAuthCode($id){
                    $action = $this->db->delete('token_code', $id);
                    return !$action->error() ? true : false ;
    }


    public function invalidateAuthCode($codeId){
        $data = ["is_expired"=>1];
        $validData = [];
        foreach ($data as $key => $value){
            if($value) $validData[$key] = $value;
        }

        $update =  $this->db->update("token_code", $codeId, $validData);
        return !$update->error() ? true: false ;
    }

    public function invalidateAcessToken($accessTokenId){
        $data = ["is_expired"=>1];
        $validData = [];
        foreach ($data as $key => $value){
            if($value) $validData[$key] = $value;
         }

        $update =  $this->db->update("access_token", $accessTokenId, $validData);
        return !$update->error() ? true: false ;
    }

    public function saveAcessToken($token){
        $fields = [
            "unique_id",
            "token_string",
            "client_id",
            "user_identity",
            "issued_at",
            "expiration_time"
         ];

         $insert = $this->db->insert("access_token", $fields, $token) ;
        
        return !$insert->error() ? true: false ;
    }

   public function getAccessToken($token) {
    $sql =  "SELECT *
                FROM access_token
                WHERE token_string=:token_string";
    
    $params = ["token_string"=>$token];

    $result = ($this->db->query($sql, $params))->results();
    return empty($result) ? [] : $result[0];
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

        public function getUserActiveTokens($userId){
            $sql =  "SELECT DISTINCT client_id
            FROM access_token
            WHERE user_identity=:user_identity
            And is_expired=0";

            $params = ["user_identity"=>$userId];

            $result = ($this->db->query($sql, $params))->results();
            return empty($result) ? [] : $result[0];
        }


    public function revokeMany($clientId, $userId){
        $sql = "UPDATE `access_token`
                    SET is_expired=1, revoked=1
                    WHERE user_identity=:user_identity
                    AND client_id=:client_id 
                    AND is_expired=0";

        $params = [];
        $params["user_identity"] =$userId;
        $params["client_id"] =$clientId;

        $result = ($this->db->query($sql, $params))->results();
        return $result ? true : false;
    }


    private function checkTable(){
        $sql =  "SELECT 1
        FROM token_code";

        $res = ($this->db->query($sql))->results();
        return $res ? true : false;
    }

    private function createTable(){
        $sql = "CREATE TABLE `access_token` (
            `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `unique_id` varchar(60) NOT NULL,
            `token_string` varchar(60) NOT NULL,
            `client_id` varchar(100) NOT NULL,
            `user_identity` varchar(60) NOT NULL,
            `issued_at` varchar(60) NOT NULL,
            `expiration_time` varchar(30) NOT NULL,
            `is_expired` tinyint(1) NOT NULL DEFAULT 0,
            `revoked` tinyint(1) NOT NULL DEFAULT 0,
            `meta` datetime NOT NULL DEFAULT current_timestamp()
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                    
                    CREATE TABLE `token_code` (
            `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `unique_id` varchar(100) NOT NULL,
            `code_string` varchar(60) NOT NULL,
            `client_id` varchar(60) NOT NULL,
            `client_redirect_url` varchar(100) NOT NULL,
            `user_identity` varchar(100) NOT NULL,
            `issued_at` varchar(60) NOT NULL,
            `expiration_time` varchar(30) NOT NULL,
            `is_expired` tinyint(1) NOT NULL DEFAULT 0,
            `meta` datetime NOT NULL DEFAULT current_timestamp()
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                    ";

        $res = ($this->db->query($sql))->results();
        return $res ? true : false;
    }
}