<?php
require('../../dao/dao.medecin.php');
$https_method=$_SERVER['REQUEST_METHOD'];
switch($https_method){
    case "GET":
        if(isset($_GET['id'])){
            $id=$_GET['id'];
            $medecin=getMedecinById($id);
            if($medecin!=null){
                deliver_response("OK",200,"Succes",$medecin);
            }else{
                deliver_response("Error",404,"Not Found");
            }
        }else{
            $medecins=getMedecins();
            deliver_response("OK",200,"Succes",$medecins);
        }
        break;
    case "POST":
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData,true);
        if(!isset($data['nom']) || !isset($data['prenom']) || !isset($data['civilite'])){
            deliver_response("Error",400,"Bad Request");
            exit;
        }
        $medecin=array(
            "nom"=>$data['nom'],
            "prenom"=>$data['prenom'],
            "civilite"=>$data['civilite']
        );
        $medecin=addMedecin($medecin);
        if($medecin){
            deliver_response("OK",201,"Created",$medecin);
        }else{
            deliver_response("Error SQL",403,"Le medecin n'a pas été ajouté");
        }
        break;
    case "PUT":
        $data=json_decode(file_get_contents('php://input'),true);
        if(!isset($data['id']) || !isset($data['nom']) || !isset($data['prenom']) || !isset($data['civilite'])){
            deliver_response("Error",400,"Bad Request");
            exit;
        }
        $medecin=array(
            "id"=>$data['id'],
            "nom"=>$data['nom'],
            "prenom"=>$data['prenom'],
            "civilite"=>$data['civilite']
        );
        $id=updateMedecin($medecin);
        if(is_numeric($id)){
            $medecin=getMedecinById($id);
            deliver_response("OK",200,"Succes",$medecin);
        }else{
            deliver_response("Error",400,"Bad Request");
        }
        break;
    case "PATCH":
        $id=$_GET['id'];
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData,true);
        if (!isset($id)){
            deliver_response("Error",400,"Bad Request, id manquant");
            exit;
        }
        $medecin=getMedecinById($id);
        if($medecin==null){
            deliver_response("Error",404,"Not Found, medecin inexistant");
            exit;
        }
        if(!checkParamPatch($data)){
            deliver_response("Error",400,"Bad Request, arguments manquants");
            exit;
        }
        $medecin=constructAndUpdate($data,$id);
        if($medecin){
            deliver_response("OK",200,"Succes, medecin modifié",$medecin);
        }else{
            deliver_response("Error",400,"Bad Request,medecin n'a pas pu être modifié".$medecin);
        }
        break;
    case "DELETE":
        if(!isset($id)){
            deliver_response("Error",400,"Bad Request,id manquant");
            exit;
        }
        $id=$_GET['id'];
        $medecin=getMedecinById($id);
        if($medecin==null){
            deliver_response("Error",404,"Not Found, medecin inexistant");
            exit;
        }
        $result=deleteMedecin($id);
        if($result){
            deliver_response("OK",200,"Succes");
        }else{
            deliver_response("Error",400,"Bad Request");
        }
        break;
    case "OPTIONS":
        deliver_response("OK",200,"Succes",null,"Allow: GET,POST,PUT,DELETE,OPTIONS");
        break;
}
function checkParamPatch($data){
    if(!isset($data['nom']) && !isset($data['prenom']) && !isset($data['civilite'])){
        return false;
    }
    return true;
}
function constructAndUpdate($data,$id){
   $ancienMedecin=getMedecinById($id);
    if(isset($data['nom'])){
         $ancienMedecin['nom']=$data['nom'];
    }
    if(isset($data['prenom'])){
        $ancienMedecin['prenom']=$data['prenom'];
    }
    if(isset($data['civilite'])){
        $ancienMedecin['civilite']=$data['civilite'];
    }
    $medecin=updateMedecin($ancienMedecin);
    return $medecin;
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