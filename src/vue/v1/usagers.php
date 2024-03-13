<?php
require('../../dao/dao.usager.php');
require('utils.php');
$https_method=$_SERVER['REQUEST_METHOD'];
$modele_usager=array("nom","prenom","civilite","adresse","code_postal","ville","date_nais","lieu_nais","num_secu","sexe");
$res=check_token();
if(!$res){
    deliver_response("Error",401,"Wrong token");
    exit;
}
switch($https_method){
    case "GET":
        if(isset($_GET['id'])){
            $id=$_GET['id'];
            $usager=getUsagerById($id);
            if($usager!=null){
                deliver_response("OK",200,"Succes",$usager);
            }else{
                deliver_response("Error",404,"Not Found");
            }
        }else{
            $usagers=getUsagers();
            deliver_response("OK",200,"Succes",$usagers);
        }
        break;
    case "POST":
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData,true);
        if(!check_usager_post($data)){
            deliver_response("Error",400,"Bad Request,arguments manquants");
            exit;
        }
        $usagerNouveau=constructUsagerPost($data);
        $usager=addUsager($usagerNouveau);
        if($usager){
            deliver_response("OK",201,"Created",$usager);
        }else{
            deliver_response("Error SQL",403,"L'usager n'a pas été ajouté");
        }
        break;
    case "PATCH":
        $data=json_decode(file_get_contents('php://input'),true);
        if(!isset($_GET['id']) || !checkUsagerParamPatch($data)){
            deliver_response("Error",400,"Bad Request,il faut au moins un paramètre à modifier");
            exit;
        }
        $id=$_GET['id'];
        $usager=constructUsagerPatch($id,$data);
        $res=updateUsager($usager);
        if($res){
            $usager=getUsagerById($id);
            deliver_response("OK",200,"Succes",$usager);
        }else{
            deliver_response("Error SQL",403,"L'usager n'a pas été modifié");
        }
        break;
    case "DELETE":
        if(!isset($_GET['id'])){
            deliver_response("Error",400,"Bad Request,id manquants");
            exit;
        }
        $id=$_GET['id'];
        $res=deleteUsager($id);
        if($res){
            deliver_response("OK",200,"Succes");
        }else{
            deliver_response("Error SQL",403,"L'usager n'a pas été supprimé");
        }
        break;
    case "OPTIONS":
        deliver_response("OK",200,"Succes",null,true);
        break;
    }
function check_usager_post($usager){
    global $modele_usager;
    foreach($modele_usager as $u){
        if(!isset($usager[$u])){
            return false;
        }
    }
    return true;
}
function constructUsagerPost($data){
    global $modele_usager;
    $usager=array();
    foreach($modele_usager as $u){
        $usager[$u]=$data[$u];
    }
    $usager['date_nais']=convertDate($usager['date_nais']);
    return $usager;
}
function constructUsagerPatch($id,$data){
    global $modele_usager;
    $usagerAncien=getUsagerById($id);
    foreach($modele_usager as $u){
        if(isset($data[$u])){
            $usagerAncien[$u]=$data[$u];
        }
    }
    $usagerAncien['date_nais']=convertDate($usagerAncien['date_nais']);
    return $usagerAncien;
    
}
function convertDate($date){
    $date = str_replace('/', '-', $date);
    return date('Y-m-d', strtotime($date));
}
function checkUsagerParamPatch($data){
    global $modele_usager;
    foreach($modele_usager as $u){
        if(isset($data[$u])){
            return true;
        }
    }
    return false;
}