<?php
require_once(__DIR__.'/../../dao/dao.consultation.php');
require_once(__DIR__.'/../../functions/utils.php');
$https_method = $_SERVER['REQUEST_METHOD'];
// CORS
if ($https_method == "OPTIONS") {
    deliver_response("OK", 204, "CORS authorized", null, true);
    exit;
}
//modele de la consultation
$modele_consultation = array("id_medecin", "id_usager", "date_consult", "heure_consult", "duree_consult");

// Vérification du token
check_token();
// Gestion des requêtes
switch ($https_method) {
        // Récupération de la liste des consultations
    case "GET":
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            verifierConsultationExiste($id);
            $consultation = getConsultationById($id);
            gestionErreurSQL($consultation);
            deliver_response("OK", 200, "Succes, voici la consultation avec l'id " . $id, $consultation);
        } else {
            $consultations = getConsultations();
            gestionErreurSQL($consultations);
            deliver_response("OK", 200, "Succes, voici toutes les consultations", $consultations);
        }
        break;
        // Création d'une consultation
    case "POST":
        // Récupération des données
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);
        // Vérification des données
        if (!check_consultation_post($data)) {
            deliver_response("Error", 400, "Bad Request,arguments manquants, impossible de créer la consultation");
            exit;
        }
        // Ajout de la consultation
        $consultationNouveau = constructConsultationPost($data);
        $consultation = addConsultation($consultationNouveau);
        gestionErreurSQL($consultation);
        $res = getConsultationById($consultation);
        deliver_response("OK", 201, "La consultation est crée", $res);
        break;
        // Modification d'une consultation
    case "PATCH":
        // Récupération des données
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($_GET['id']) || !checkConsultationParamPatch($data)) {
            deliver_response("Error", 400, "Bad Request,il faut au moins un paramètre à modifier");
            exit;
        }
        // Vérification de l'existence de la consultation
        $id = $_GET['id'];
        verifierConsultationExiste($id);
        // Modification de la consultation
        $consultation = constructConsultationPatch($id, $data);
        $res = updateConsultation($consultation);

        // Gestion des erreurs SQL
        gestionErreurSQL($res);

        // Récupération de la consultation modifiée
        $consultation = getConsultationById($id);
        deliver_response("OK", 200, "Succes, consultation avec l'id {$id} est modifiée", $consultation);
        break;
        // Suppression d'une consultation
    case "DELETE":
        // Vérification de l'existence de la consultation
        if (!isset($_GET['id'])) {
            deliver_response("Error", 400, "Bad Request,id manquants");
            exit;
        }
        $id = $_GET['id'];
        verifierConsultationExiste($id);
        $res = deleteConsultation($id);
        gestionErreurSQL($res);
        deliver_response("OK", 200, "Succes, la consultation est supprimée");
        break;
    default:
        deliver_response("Error", 405, "Method Not Allowed");
        break;
}

//Fonctions de vérification post
function check_consultation_post($data)
{
    global $modele_consultation;
    foreach ($modele_consultation as $key) {
        if (!isset($data[$key])) {
            return false;
        }
    }
    checkdateetHeureRDv($data['heure_consult'],$data['date_consult']);
    return true;
}
//Fonctions de construction de la consultation
function constructConsultationPost($data)
{
    global $modele_consultation;
    checkheureValid($data['heure_consult']);
    $consultation = array();
    foreach ($modele_consultation as $key) {
        $consultation[$key] = $data[$key];
    }
    $consultation['date_consult'] = convertDate($consultation['date_consult']);
    $consultation['heure_consult'] = convertTime($consultation['heure_consult']);
    return $consultation;
}
//Fonctions de vérification patch
function checkConsultationParamPatch($data)
{
    global $modele_consultation;
    foreach ($modele_consultation as $key) {
        if (isset($data[$key])) {
            return true;
        }
    }

    return false;
}
//Fonctions de construction de la consultation avec patch
function constructConsultationPatch($id, $data)
{
    global $modele_consultation;
    $consultationAncienne = getConsultationById($id);
    foreach ($modele_consultation as $key) {
        if (isset($data[$key])) {
            $consultationAncienne[$key] = $data[$key];
        }
    }
    checkdateetHeureRDv($data['heure_consult'],$data['date_consult']);
    $consultationAncienne['date_consult'] = convertDate($consultationAncienne['date_consult']);
    $consultationAncienne['heure_consult'] = convertTime($consultationAncienne['heure_consult']);
    return $consultationAncienne;
}
//Fonctions de conversion de date et heure
function convertTime($time)
{
    return date('H:i', strtotime($time));
}
function verifierConsultationExiste($id)
{
    $consultation = getConsultationById($id);
    if (!$consultation) {
        deliver_response("Error", 404, "Consultation non trouvée");
        exit;
    }
}
