<?php
require('../../../dao/dao.stats.php');
require('../utils.php');
$https_method=$_SERVER['REQUEST_METHOD'];
if($https_method=="OPTIONS"){
    deliver_response("OK",204,"CORS authorized",null,true);
    exit;
}
check_token();
switch($https_method){
    case "GET":
        $stats=getStatsUsagers();
        gestionErreurSQL($stats);
        deliver_response("OK",200,"Voici les stats des usagers",$stats);
        break;
    default:
        deliver_response("Error",405,"Method Not Allowed");
        break;
}