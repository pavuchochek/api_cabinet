<?php
require_once("jwt_utils.php");
function deliver_response($status, $status_code, $status_message, $data = null, $options = null)
{
    /// Paramétrage de l'entête HTTP
    header("Access-Control-Allow-Origin: *");
    if ($options) {
        header("Access-Control-Allow-Methods: *");
        header("Access-Control-Allow-Headers:*");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Origin: *");
    }
    http_response_code($status_code); //Utilise un message standardisé en

    header("HTTP/1.1 $status_code $status_message");
    header("Content-Type:application/json; charset=utf-8");
    $response['status'] = $status;
    $response['status_code'] = $status_code;
    $response['status_message'] = $status_message;
    if ($data) {
        $response['data'] = $data;
    }
    /// Mapping de la réponse au format JSON
    $json_response = json_encode($response);
    if ($json_response === false)
        die('json encode ERROR : ' . json_last_error_msg());
    /// Affichage de la réponse (Retourné au client)
    echo $json_response;
}
function check_token()
{
    $env = parse_ini_file(__DIR__ . '/../../.env.url');
    $url_auth = $env["URL_AUTH"];
    $curl_h = curl_init($url_auth);
    $token = get_bearer_token();
    if (!$token) {
        return false;
    }
    curl_setopt(
        $curl_h,
        CURLOPT_HTTPHEADER,
        array(
            'Authorization: Bearer ' . $token,
        )
    );

    # do not output, but store to variable
    curl_setopt($curl_h, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl_h);
    $httpcode = curl_getinfo($curl_h, CURLINFO_HTTP_CODE);
    curl_close($curl_h);
    if ($httpcode != 204) {
        deliver_response("Error", 401, "Token incorrect");
        exit;
    }
}
function gestionErreurSQL($res)
{
    if (isset($res["error"])) {
        $statut = "Error";
        $code = 500;
        if ($res["error"] == 1062) {
            $statut = "Chevauchement de clé primaire";
            $code = 409;
        }
        deliver_response($statut, $code, "SQL[{$res["error"]}] " . $res["info"]);
        exit;
    }
}
function convertDate($date)
{
    //type de date (jj/mm/aa)
    $jour = substr($date, 0, 2);
    $mois = substr($date, 3, 2);
    $annee = substr($date, 6, 4);
    return $annee . "-" . $mois . "-" . $jour;
}
// Vérification de l'existence de l'usager
function verifierUsagerNonExistant($id)
{
    $usager = getUsagerById($id);
    if (!isset($usager["id"])) {
        return true;
    } else {
        return false;
    }
}

function verificationMedecinNonExistant($id, $message)
{
    $medecin = getMedecinById($id);
    if ($medecin == null) {
        deliver_response("Error", 404, $message);
        exit;
    }
}
function checkdateValidDateNaiss($date)
{
    // Séparer la date en jour, mois et année
    $date_parts = explode("/", $date);

    // Vérifier s'il y a trois parties (jour, mois, année)
    if (count($date_parts) != 3) {
        deliver_response("Error", 400, "Date invalide");
        exit;
    }

    // Récupérer le jour, le mois et l'année
    $day = intval($date_parts[0]);
    $month = intval($date_parts[1]);
    $year = intval($date_parts[2]);

    checkdateNaiss($month, $year, $day);
    // Vérifier si la date est valide avec checkdate
    if (!checkdate($month, $day, $year)) {
        deliver_response("Error", 400, "Date invalide");
        exit;
    }
}
function checkdateetHeureRDv($heure, $date)
{
    // Séparer la date en jour, mois et année
    $date_parts = explode("/", $date);
    checkheureValid($heure);
    // Vérifier s'il y a trois parties (jour, mois, année)
    if (count($date_parts) != 3) {
        deliver_response("Error", 400, "Date invalide");
        exit;
    }

    // Récupérer le jour, le mois et l'année
    $year = intval($date_parts[0]);
    $month = intval($date_parts[1]);
    $day = intval($date_parts[2]);
    $h = explode(":", $heure);
    $hour = intval($h[0]);
    $minute = intval($h[1]);
    // Créer un objet de date pour la date fournie
    $input_date = mktime($hour,$minute, $month, $day, $year);

    // Créer un objet de date pour la date actuelle
    $current_date = mktime(date('H'),date('i'),date("m"), date("d"), date("Y"));

    // Comparer les dates
    if ($input_date < $current_date) {
        deliver_response("Error", 400, "Date invalide, on ne peut pas prendre un rendez-vous dans le passé");
        exit;
    }
}
function checkheureValid($heure)
{
    // Séparer l'heure en heures et minutes
    $heure_parts = explode(":", $heure);

    // Vérifier s'il y a deux parties (heures, minutes)
    if (count($heure_parts) != 2) {
        deliver_response("Error", 400, "Heure invalide");
        exit;
    }

    // Récupérer les heures et les minutes
    $hour = intval($heure_parts[0]);
    $minute = intval($heure_parts[1]);
    if (!ctype_digit($heure_parts[0]) || !ctype_digit($heure_parts[1])) {
        deliver_response("Error", 400, "Heure invalide");
        exit;
    }
    // Vérifier si les heures et les minutes sont valides
    if ($hour < 0 || $hour > 23 || $minute < 0 || $minute > 59) {
        deliver_response("Error", 400, "Heure invalide");
        exit;
    }
}
function checkdateNaiss($month,$year,$day){
    // Créer un objet de date pour la date fournie
    $input_date = mktime(0,0,0, $month, $day, $year);

    // Créer un objet de date pour la date actuelle
    $current_date = mktime(date('H'),date('i'),date("m"), date("d"), date("Y"));

    // Comparer les dates
    if ($input_date > $current_date) {
       deliver_response("Error", 400, "Date naissance invalide, on ne peut pas être né dans le futur");
       exit;
    }
    if($year<1820){
        deliver_response("Error", 400, "Date naissance invalide, on ne peut pas être né avant 1820");
        exit;
    }
}