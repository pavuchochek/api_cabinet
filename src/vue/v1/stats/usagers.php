<?php
require('../../../dao/dao.stats.php');
require('utils.php');
$https_method=$_SERVER['REQUEST_METHOD'];
$modele_consultation=array("id_medecin","id_usager","date_consult","heure_consult","duree_consult");
$res=check_token();
if(!$res){
    deliver_response("Error",401,"Wrong token");
    exit;
}