<?php
require('../../dao/dao.consultation.php');
require('utils.php');
$https_method=$_SERVER['REQUEST_METHOD'];
$modele_consultation=array("id_medecin","id_usager","date_consult","heure_consult","duree_consult");
$res=check_token();
if(!$res){
    deliver_response("Error",401,"Wrong token");
    exit;
}
switch($https_method){
    case "GET":
        if(isset($_GET['id'])){
            $id=$_GET['id'];
            $consultation=getConsultationById($id);
            if($consultation!=null && !($consultation instanceof PDOException)){
                deliver_response("OK",200,"Succes voici la consultation ".$id,$consultation);
            }else{
                deliver_response("Error",404,"Not Found");
            }
        }else{
            $consultations=getConsultations();
            if($consultations instanceof PDOException){
                deliver_response("Error SQL",403,"Erreur lors de la récupération des consultations",$consultations->getMessage());
            }else{
                deliver_response("OK",200,"Succes",$consultations);
            }
        }
        break;
    case "POST":
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData,true);
        if(!check_consultation_post($data)){
            deliver_response("Error",400,"Bad Request,arguments manquants");
            exit;
        }
        $consultationNouveau=constructConsultationPost($data);
        $consultation=addConsultation($consultationNouveau);
        
        if(!$consultation instanceof PDOException){
            $res=getConsultationById($consultation);
            deliver_response("OK",201,"Created",$res);
        }else{
            deliver_response("Error SQL",403,"La consultation n'a pas été ajoutée ",$consultation->getMessage());
        }
        break;
    case "PATCH":
        $data=json_decode(file_get_contents('php://input'),true);
        if(!isset($_GET['id']) || !checkConsultationParamPatch($data)){
            deliver_response("Error",400,"Bad Request,il faut au moins un paramètre à modifier");
            exit;
        }
        $id=$_GET['id'];
        $consultationPossible=getConsultationById($id);
        if(!$consultationPossible){
            deliver_response("Error",404,"Not Found");
            exit;
        }
        $consultation=constructConsultationPatch($id,$data);
        $res=updateConsultation($consultation);
        if(!$res instanceof PDOException){
            $consultation=getConsultationById($id);
            deliver_response("OK",200,"Succes",$consultation);
        }else{
            deliver_response("Error SQL",403,"La consultation n'a pas été modifiée",$res->getMessage());
        }
        break;
    case "DELETE":
        if(!isset($_GET['id'])){
            deliver_response("Error",400,"Bad Request,id manquants");
            exit;
        }
        $id=$_GET['id'];
        $consultation=getConsultationById($id);
        if(!$consultation){
            deliver_response("Error",404,"Not Found");
            exit;
        }
        $res=deleteConsultation($id);
        if(!$res instanceof PDOException){
            deliver_response("OK",200,"Succes");
        }else{
            deliver_response("Error SQL",403,"La consultation n'a pas été supprimée",$res->getMessage());
        }
        break;
    case "OPTIONS":
        deliver_response("OK",200,"Succes",null,true);
        break;
    }

    function check_consultation_post($data){
        global $modele_consultation;
        foreach($modele_consultation as $key){
            if(!isset($data[$key])){
                return false;
            }
        }
        return true;
    }
    function constructConsultationPost($data){
        global $modele_consultation;
        $consultation=array();
        foreach($modele_consultation as $key){
            $consultation[$key]=$data[$key];
        }
        $consultation['date_consult']=convertDate($consultation['date_consult']);
        $consultation['heure_consult']=convertTime($consultation['heure_consult']);
        return $consultation;
    }
    function checkConsultationParamPatch($data){
        global $modele_consultation;
        foreach($modele_consultation as $key){
            if(isset($data[$key])){
                return true;
            }
        }
        return false;
    }
    function constructConsultationPatch($id,$data){
        global $modele_consultation;
        $consultationAncienne=getConsultationById($id);
        foreach($modele_consultation as $key){
            if(isset($data[$key])){
                $consultationAncienne[$key]=$data[$key];
            }
        }
        $consultationAncienne['date_consult']=convertDate($consultationAncienne['date_consult']);
        $consultationAncienne['heure_consult']=convertTime($consultationAncienne['heure_consult']);
        return $consultationAncienne;
    }
    
    function convertDate($date){
        $date = str_replace('/', '-', $date);
        return date('Y-m-d', strtotime($date));
    }
    function convertTime($time){
        return date('H:i', strtotime($time));
    }