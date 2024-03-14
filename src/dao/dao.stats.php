<?php
require('Connexion.php');
function getStatsMedecins()
{
    try {
        $linkpdo = Connexion::getInstance();
        $query = "SELECT 
        CONCAT(medecin.nom, ' ', medecin.prenom) AS medecin_nom_prenom,
        SUM(consultation.duree_consult) AS duree_totale FROM medecin JOIN consultation ON medecin.id_medecin = consultation.id_medecin
        WHERE consultation.date_consult <= CURDATE() -- Filtre par rapport à la date actuelle 
        GROUP BY medecin.nom, medecin.prenom;";
        $stmt = $linkpdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stats = array();
        foreach ($result as $row) {
            $stat = array(
                "medecin_nom_prenom" => $row['medecin_nom_prenom'],
                "duree_totale" => $row['duree_totale']
            );
            array_push($stats, $stat);
        }
        $linkpdo = null;
        return $stats;
    } catch (PDOException $e) {
        return $e;
    }
}
function getStatsUsagers()
{
    try {
        $linkpdo = Connexion::getInstance();
        $query = "SELECT 
        CONCAT(usager.nom, ' ', usager.prenom) AS usager_nom_prenom,
        COUNT(consultation.id_usager) AS nb_consultations FROM usager JOIN consultation ON usager.id_usager = consultation.id_usager
        WHERE consultation.date_consult <= CURDATE() -- Filtre par rapport à la date actuelle 
        GROUP BY usager.nom, usager.prenom;";
        $stmt = $linkpdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stats = array();
        foreach ($result as $row) {
            $stat = array(
                "usager_nom_prenom" => $row['usager_nom_prenom'],
                "nb_consultations" => $row['nb_consultations']
            );
            array_push($stats, $stat);
        }
        $linkpdo = null;
        return $stats;
    } catch (PDOException $e) {
        return $e;
    }
}