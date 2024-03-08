<?php
include('Connexion.php');
function getConsultations(){
    try{
        $linkpdo=Connexion::getInstance();
        $query="SELECT * FROM consultation";
        $stmt=$linkpdo->prepare($query);
        $stmt->execute();
        $result=$stmt->fetchAll();
        $consultations=array();
        foreach($result as $row){
            $consultation=array(
                "id"=>$row['id_consult'],
                "date"=>$row['date_consult'],
                "heure"=>$row['heure_consult'],
                "duree"=>$row['duree_consult'],
                "id_medecin"=>$row['id_medecin'],
                "id_usager"=>$row['id_usager']
            );
            array_push($consultations,$consultation);
        }
        $linkpdo=null;
        return $consultations;
    }
    catch(PDOException $e){
        return $e->getMessage();
    }
}
function getConsultationById($id){
    try{
        $linkpdo=Connexion::getInstance();
        $query="SELECT * FROM consultation WHERE id_consult=:id";
        $stmt=$linkpdo->prepare($query);
        $stmt->bindParam(':id',$id);
        $stmt->execute();
        $result=$stmt->fetch();
        if($result){
            $consultation=array(
                "id"=>$result['id_consult'],
                "date"=>$result['date_consult'],
                "heure"=>$result['heure_consult'],
                "duree"=>$result['duree_consult'],
                "id_medecin"=>$result['id_medecin'],
                "id_usager"=>$result['id_usager']
            );
            $linkpdo=null;
            return $consultation;
        }else{
            $linkpdo=null;
            return null;
        }
    }
    catch(PDOException $e){
        return $e->getMessage();
    }
}
function addConsultation($consultation){
    try{
        $linkpdo=Connexion::getInstance();
        $query="INSERT INTO consultation(date_consult,heure_consult,duree_consult,id_medecin,id_usager) VALUES(:date,:heure,:duree,:id_medecin,:id_usager)";
        $stmt=$linkpdo->prepare($query);
        $stmt->bindParam(':date',$consultation['date']);
        $stmt->bindParam(':heure',$consultation['heure']);
        $stmt->bindParam(':duree',$consultation['duree']);
        $stmt->bindParam(':id_medecin',$consultation['id_medecin']);
        $stmt->bindParam(':id_usager',$consultation['id_usager']);
        $stmt->execute();
        $id=$linkpdo->lastInsertId();
        $linkpdo=null;
        return $id;
    }
    catch(PDOException $e){
        return $e->getMessage();
    }
}
function deleteConsultation($id){
    try{
        $linkpdo=Connexion::getInstance();
        $query="DELETE FROM consultation WHERE id_consult=:id";
        $stmt=$linkpdo->prepare($query);
        $stmt->bindParam(':id',$id);
        $stmt->execute();
        $linkpdo=null;
        return true;
    }
    catch(PDOException $e){
        return $e->getMessage();
    }
}
function updateConsultation($consultation){
    try{
        $linkpdo=Connexion::getInstance();
        $query="UPDATE consultation SET date_consult=:date,heure_consult=:heure,duree_consult=:duree,id_medecin=:id_medecin,id_usager=:id_usager WHERE id_consult=:id";
        $stmt=$linkpdo->prepare($query);
        $stmt->bindParam(':id',$consultation['id']);
        $stmt->bindParam(':date',$consultation['date']);
        $stmt->bindParam(':heure',$consultation['heure']);
        $stmt->bindParam(':duree',$consultation['duree']);
        $stmt->bindParam(':id_medecin',$consultation['id_medecin']);
        $stmt->bindParam(':id_usager',$consultation['id_usager']);
        $stmt->execute();
        $linkpdo=null;
        return true;
    }
    catch(PDOException $e){
        return $e->getMessage();
    }
}