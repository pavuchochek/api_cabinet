<?php
require_once('Connexion.php');
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
        $result = array("error" => $e->errorInfo[1]);
        $result["info"] = "Erreur SQL, contactez l'administrateur";
        return $result;
    }
}
function getStatsUsagers()
{
    try {
        $linkpdo = Connexion::getInstance();
        $query = "SELECT
        genre,
        SUM(CASE WHEN age < 25 THEN 1 ELSE 0 END) AS moins_de_25_ans,
        SUM(CASE WHEN age >= 25 AND age <= 50 THEN 1 ELSE 0 END) AS entre_25_et_50_ans,
        SUM(CASE WHEN age > 50 THEN 1 ELSE 0 END) AS plus_de_50_ans
    FROM (
        SELECT
            sexe AS genre,
            YEAR(CURRENT_DATE) - YEAR(date_nais) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(date_nais, '%m%d')) AS age
        FROM
            usager
    ) AS age_utilisateurs
    GROUP BY
        genre;
    ";
        $stmt = $linkpdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stats = array();
        foreach ($result as $row) {
            $stat = array(
                "genre" => $row['genre'],
                "moins_de_25_ans" => $row['moins_de_25_ans'],
                "entre_25_et_50_ans" => $row['entre_25_et_50_ans'],
                "plus_de_50_ans" => $row['plus_de_50_ans']
            );
            array_push($stats, $stat);
        }
        $linkpdo = null;
        return $stats;
    } catch (PDOException $e) {
        $result = array("error" => $e->errorInfo[1]);
        $result["info"] = "Erreur SQL, contactez l'administrateur";
        return $result;
    }
}