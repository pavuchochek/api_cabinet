<?php
require_once(__DIR__.'/../../dao/dao.medecin.php');
require_once(__DIR__.'/../../functions/utils.php');
$https_method=$_SERVER['REQUEST_METHOD'];
// CORS
if($https_method=="OPTIONS"){
    deliver_response("OK",204,"CORS authorized",null,true);
    exit;
}
// Vérification du token
check_token();

// Gestion des requêtes
switch($https_method){
    // Récupération de la liste des medecins
    case "GET":
        if(isset($_GET['id'])){
            $id=$_GET['id'];
            verificationMedecinNonExistant($id,"Medecin non trouvé");
            $medecin=getMedecinById($id);
            deliver_response("OK",200,"Succes, voici le medecin avec l'id ".$id,$medecin);
        }else{
            $medecins=getMedecins();
            deliver_response("OK",200,"Succes, voici tout les medecins",$medecins);
        }
        break;
    // Création d'un medecin
    case "POST":
        // Récupération des données
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData,true);
        // Vérification des données
        if(!isset($data['nom']) || !isset($data['prenom']) || !isset($data['civilite'])){
            deliver_response("Error",400,"Arguments manquants");
            exit;
        }
        $medecin=array(
            "nom"=>$data['nom'],
            "prenom"=>$data['prenom'],
            "civilite"=>$data['civilite']
        );
        // Ajout de l'usager
        $medecin=addMedecin($medecin);
        //gestion des erreurs SQL
        gestionErreurSQL($medecin);
        deliver_response("OK",201,"Created",$medecin);
        break;
    // Modification d'un medecin
    case "PATCH":
        if(!isset($_GET['id'])){
            deliver_response("Error",400,"Bad Request,id manquant");
            exit;
        }
        $id=$_GET['id'];
        // Récupération des données
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData,true);
        // Vérification des données
        verificationMedecinNonExistant($id,"Medecin non trouvé");
        if(!checkParamPatch($data)){
            deliver_response("Error",400,"Bad Request, arguments manquants");
            exit;
        }
        // Mise à jour de l'usager
        $medecin=constructAndUpdate($data,$id);
        //gestion des erreurs SQL
        gestionErreurSQL($medecin);
        // Retour de la réponse
        deliver_response("OK",200,"Succes, medecin modifié",$medecin);
        break;
    case "DELETE":
        // Récupération des données
        if(!isset($_GET['id'])){
            deliver_response("Error",400,"Bad Request,id manquant");
            exit;
        }
        $id=$_GET['id'];
        // Vérification de l'existence de l'usager
        verificationMedecinNonExistant($id,"Medecin non trouvé");
        // Suppression de l'usager
        $result=deleteMedecin($id);
        //gestion des erreurs SQL
        gestionErreurSQL($result);
        // Retour de la réponse
        deliver_response("OK",200,"L'usager {$id} a été supprimé avec succès");
        break;
    default:
        deliver_response("Error",405,"Méthode non autorisée");
        break;
}
// Fonctions supplémentaires

// Vérification des paramètres pour la modification
function checkParamPatch($data){
    if(!isset($data['nom']) && !isset($data['prenom']) && !isset($data['civilite'])){
        return false;
    }
    return true;
}
// Construction et mise à jour de l'usager
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