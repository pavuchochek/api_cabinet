<?php
include_once('../../dao/ConnexionAuth.php');

include_once('jwt_utils.php');
    $http_method = $_SERVER['REQUEST_METHOD'];
    $linkpdo=ConnexionAuth::getInstance();
    switch ($http_method){
        case "POST" :
            $postedData = file_get_contents('php://input');
            $data = json_decode($postedData,true);
            if(!isset($data['login']) || !isset($data['mdp'])){
                deliver_response("Error", 400,"Bad Request");
            }else{
                $login = $data['login'];
                $password = $data['mdp'];
                //REQUETE POUR RETROUVER LOGIN ET MDP
                $query = "SELECT * FROM user_auth_v1 WHERE login = :login";
                $stmt = $linkpdo->prepare($query);
                $stmt->bindParam(':login', $login);
                
                $stmt->execute();
                $result = $stmt->fetch();
                //si on trouve
                if($result){
                    $vi=password_hash($password,PASSWORD_DEFAULT);
                    if(password_verify($password,$result["mdp"])){
                        //on choisit l'encodage
                        $headers = array(
                            "alg" => "HS256",
                            "typ" => "JWT"
                        );
                        //on rempli le payload avec le login et l'expiration
                        $payload = array(
                            "login" => $login,
                            "exp" => time() + 3600
                        );
                        //et le mot qu'on va encoder
                        $secret = "secret";
                        //on genere le token
                        $jwt = generate_jwt($headers, $payload, $secret);
                        //et on l'envoi
                        deliver_response("OK", 200, "Succes",$jwt);
                    }else{
                        deliver_response("Error", 401,"Unauthorized");
                    }
                }else{
                    deliver_response("Error", 401,"Unauthorized");
                }
            }
            break;
        case "GET":
            $token=get_bearer_token();
            if(!is_jwt_valid($token,"secret")){
                deliver_response("Error", 401,"Token exprired or invalid");
                return;
            }else{
                $info_user=getInfoFromToken($token);
                deliver_response("OK", 201, "Succes",$info_user);
            }

    }
    
function deliver_response($status,$status_code, $status_message, $data=null,$options=null){
    /// Paramétrage de l'entête HTTP
    http_response_code($status_code); //Utilise un message standardisé en
    if($options){
        header("Access-Control-Allow-Methods: *");
        header("Access-Control-Allow-Headers: *");
    }
    header("HTTP/1.1 $status_code $status_message"); 
    header("Content-Type:application/json; charset=utf-8");
    header("Access-Control-Allow-Origin: *");
    $response['status']=$status;
    $response['status_code'] = $status_code;
    $response['status_message'] = $status_message;
    if($data){
        $response['data'] = $data;
    }
    /// Mapping de la réponse au format JSON
    $json_response = json_encode($response);
    if($json_response===false)
    die('json encode ERROR : '.json_last_error_msg());
    /// Affichage de la réponse (Retourné au client)
    echo $json_response;
    }
        
?>