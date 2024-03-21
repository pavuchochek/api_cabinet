<?php
require_once(__DIR__.'/../../../dao/dao.stats.php');
require_once(__DIR__.'/../../../functions/utils.php');
$https_method=$_SERVER['REQUEST_METHOD'];
if($https_method=="OPTIONS"){
    deliver_response("OK",204,"CORS authorized",null,true);
    exit;
}
check_token();

switch($https_method){
    case "GET":
        $stats=getStatsMedecins();
        gestionErreurSQL($stats);
        deliver_response("OK",200,"Voici les stats des medecins",$stats);
        break;
    default:
        deliver_response("Error",405,"Method Not Allowed");
        break;
}