<?php
require('../../dao/dao.medecin.php');
require('utils.php');
$https_method=$_SERVER['REQUEST_METHOD'];
header("Access-Control-Allow-Methods: DELETE, POST, GET, OPTIONS, PATCH");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
$res=check_token();
if(!$res){
    deliver_response("Error",401,"Wrong token");
    exit;
}
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
        if(!isset($_GET['id'])){
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
        
        deliver_response("OK",204,"CORS authorized");
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
?>