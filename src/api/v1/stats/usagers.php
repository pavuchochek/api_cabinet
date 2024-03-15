<?php
require('../../../dao/dao.stats.php');
require('../utils.php');
$https_method=$_SERVER['REQUEST_METHOD'];
$modele_consultation=array("id_medecin","id_usager","date_consult","heure_consult","duree_consult");
$res=check_token();
if(!$res){
    deliver_response("Error",401,"Wrong token");
    exit;
}
switch($https_method){
    case "GET":
        $stats=getStatsUsagers();
        if($stats instanceof PDOException){
            deliver_response("Error SQL",403,"Erreur lors de la récupération des statistiques",$stats->getMessage());
        }else{
            deliver_response("OK",200,"Succes",$stats);
        }
        break;
    case"OPTIONS":
        header("Access-Control-Allow-Methods: *");
        header("Access-Control-Allow-Headers:*");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Origin: *");
        deliver_response("OK",204,"Succes",null);
        break;
    default:
        deliver_response("Error",405,"Method Not Allowed");
        break;
}