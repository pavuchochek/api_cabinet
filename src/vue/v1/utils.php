<?php
require("__DIR__/../../auth/jwt_utils.php");
function deliver_response($status,$status_code, $status_message, $data=null,$options=null){
    /// Paramétrage de l'entête HTTP
    http_response_code($status_code); //Utilise un message standardisé en
    header("Access-Control-Allow-Methods: DELETE, POST, GET, OPTIONS, PATCH");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Credentials: true");

    header("HTTP/1.1 $status_code $status_message"); 
    header("Content-Type:application/json; charset=utf-8");
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
function check_token(){
    $env = parse_ini_file('__DIR__/../../.env.url');
    $url_auth = $env["URL_AUTH"];
    $curl_h = curl_init($url_auth);
    $token=get_bearer_token();
    if(!$token){
        return false;
    }
    curl_setopt($curl_h, CURLOPT_HTTPHEADER,
        array(
            'Authorization: Bearer '.$token,
        )
    );

    # do not output, but store to variable
    curl_setopt($curl_h, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl_h);
    $httpcode = curl_getinfo($curl_h, CURLINFO_HTTP_CODE);
    curl_close($curl_h);
    if($httpcode==200){
        return true;
    }else{
        return false;
    }
}