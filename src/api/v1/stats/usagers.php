<?php
require('../../../dao/dao.stats.php');
require('../utils.php');
$https_method=$_SERVER['REQUEST_METHOD'];
if($https_method=="OPTIONS"){
    deliver_response("OK",204,"CORS authorized",null,true);
    exit;
}
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
    default:
        deliver_response("Error",405,"Method Not Allowed");
        break;
}