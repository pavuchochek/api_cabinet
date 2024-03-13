<?php
require('Connexion.php');
function getStats(){
    try{
        $linkpdo=Connexion::getInstance();
        $query="SELECT CONCAT(medecin.nom, ' ', medecin.prenom) AS medecin_nom_prenom,SUM(consultation.duree_consult) AS duree_totale FROM consultation
 JOIN medecin ON consultation.id_medecin = medecin.id_medecin
 WHERE consultation.date_consult <= CURDATE() -- Filtre par rapport à la date actuelle
 GROUP BY medecin.nom, medecin.prenom;";
        $stmt=$linkpdo->prepare($query);
        $stmt->execute();
        $result=$stmt->fetchAll();
        $stats=array();
        foreach($result as $row){
            $stat=array(
                "medecin_nom_prenom"=>$row['medecin_nom_prenom'],
                "duree_totale"=>$row['duree_totale']
            );
            array_push($stats,$stat);
        }
        $linkpdo=null;
        return $stats;
    }catch(PDOException $e){
        return $e;
    }
}