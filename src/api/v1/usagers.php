<?php
require('../../dao/dao.usager.php');
require('utils.php');

$https_method=$_SERVER['REQUEST_METHOD'];

// CORS
if($https_method=="OPTIONS"){
    deliver_response("OK",204,"CORS authorized",null,true);
    exit;
}
// Modèle de données de l'usager
$modele_usager=array("nom","prenom","civilite","adresse","code_postal","ville","date_nais","lieu_nais","num_secu","sexe");

// Vérification du token
$res=check_token();
if(!$res){
    deliver_response("Error",401,"Wrong token");
    exit;
}
// Gestion des requêtes
switch($https_method){
    // Récupération de la liste des usagers
    case "GET":
        if(isset($_GET['id'])){
            $id=$_GET['id'];
            $usager=getUsagerById($id);
            gestionErreurSQL($usager);
            if($usager["id"]==$id){
                deliver_response("OK",200,"Succes",$usager);
            }else{
                deliver_response("Erreur",404,"Usager non trouvé");
            }
        }else{
            $usagers=getUsagers();
            gestionErreurSQL($usagers);
            deliver_response("OK",200,"Succes",$usagers);
        }
        break;
    // Création d'un usager
    case "POST":
        // Récupération des données
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData,true);
        // Vérification des données
        if(!check_usager_post($data)){
            deliver_response("Erreur",400,"Bad Request,arguments manquants");
            exit;
        }
        // Construction de l'usager
        $usagerNouveau=constructUsagerPost($data);
        // Ajout de l'usager
        $usager=addUsager($usagerNouveau);
        // Gestion des erreurs
        gestionErreurSQL($usager);
        // Récupération de l'usager
        deliver_response("OK",201,"Usager crée",$usager);
        break;
    // Modification d'un usager
    case "PATCH":
        // Récupération des données
        $data=json_decode(file_get_contents('php://input'),true);
        // Vérification des données
        if(!isset($_GET['id']) || !checkUsagerParamPatch($data)){
            deliver_response("Error",400,"Bad Request,il faut au moins un paramètre à modifier");
            exit;
        }
        // Construction de l'usager
        $id=$_GET['id'];
        // Vérification de l'existence de l'usager
        if(verifierUsagerNonExistant($id)){
            deliver_response("Error",404,"Usager non trouvé");
            exit;
        }
        // Mise à jour de l'usager
        $usager=constructUsagerPatch($id,$data);
        $res=updateUsager($usager);
        // Gestion des erreurs
        gestionErreurSQL($res);
        // Récupération de l'usager
        $usager=getUsagerById($id);
        deliver_response("OK",200,"Succes",$usager);
        break;
    // Suppression d'un usager
    case "DELETE":
        // Vérification de l'existence de l'usager
        if(!isset($_GET['id'])){
            deliver_response("Error",400,"Bad Request,id manquants");
            exit;
        }
        $id=$_GET['id'];
        if(verifierUsagerNonExistant($id)){
            deliver_response("Error",404,"Usager non trouvé");
            exit;
        }
        // Suppression de l'usager
        $res=deleteUsager($id);
        // Gestion des erreurs
        gestionErreurSQL($res);
        deliver_response("OK",200,"Succes");
        break;
    }


// Fonctions de vérification et de construction des données

// Vérification des données de l'usager pour la création
function check_usager_post($usager){
    global $modele_usager;
    foreach($modele_usager as $u){
        if(!isset($usager[$u])){
            return false;
        }
    }
    return true;
}
// Construction de l'usager pour la création
function constructUsagerPost($data){
    global $modele_usager;
    $usager=array();
    foreach($modele_usager as $u){
        $usager[$u]=$data[$u];
    }
    $usager['date_nais']=convertDate($usager['date_nais']);
    return $usager;
}
// Construction de l'usager pour la modification
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
// Vérification des données de l'usager pour la modification
function checkUsagerParamPatch($data){
    global $modele_usager;
    foreach($modele_usager as $u){
        if(isset($data[$u])){
            return true;
        }
    }
    return false;
}
// Vérification de l'existence de l'usager
function verifierUsagerNonExistant($id){
    $usager=getUsagerById($id);
    if(!isset($usager["id"])){
        return true;
    }else{
        return false;
    }
}