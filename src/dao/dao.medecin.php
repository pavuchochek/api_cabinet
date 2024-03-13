<?php
include('Connexion.php');
function getMedecins(){
    try{
        $linkpdo=Connexion::getInstance();
        $query="SELECT * FROM medecin";
        $stmt=$linkpdo->prepare($query);
        $stmt->execute();
        $result=$stmt->fetchAll();
        $medecins=array();
        foreach($result as $row){
            $medecin=array(
                "id"=>$row['id_medecin'],
                "nom"=>$row['nom'],
                "prenom"=>$row['prenom'],
                "specialite"=>$row['civilite']
            );
            array_push($medecins,$medecin);
        }
        $linkpdo=null;
        return $medecins;
    }catch(PDOException $e){
        return $e;
    }
}
function getMedecinById($id){
    try{
        $linkpdo=Connexion::getInstance();
        $query="SELECT * FROM medecin WHERE id_medecin=:id";
        $stmt=$linkpdo->prepare($query);
        $stmt->bindParam(':id',$id);
        $stmt->execute();
        $result=$stmt->fetch();
        if($result){
            $medecin=array(
                "id"=>$result['id_medecin'],
                "nom"=>$result['nom'],
                "prenom"=>$result['prenom'],
                "civilite"=>$result['civilite']
            );
            $linkpdo=null;
            return $medecin;
        }else{
            $linkpdo=null;
            return null;
        }
    }catch(PDOException $e){
        return $e;
    }
}
function addMedecin($medecin){
    try{
        $linkpdo=Connexion::getInstance();
        $query="INSERT INTO medecin(nom,prenom,civilite) VALUES(:nom,:prenom,:civilite)";
        $stmt=$linkpdo->prepare($query);
        $stmt->bindParam(':nom',$medecin['nom'],PDO::PARAM_STR);
        $stmt->bindParam(':prenom',$medecin['prenom'],PDO::PARAM_STR);
        $stmt->bindParam(':civilite',$medecin['civilite'],PDO::PARAM_STR);
        $linkpdo->beginTransaction();
        $stmt->execute();
        $id=$linkpdo->lastInsertId();
        $linkpdo->commit();
        $linkpdo=null;
        $data=getMedecinById($id);
        return $data;
    }catch(PDOException $e){
        return $e;
    }
}
function deleteMedecin($id){
    try{
        $linkpdo=Connexion::getInstance();
        $query="DELETE FROM medecin WHERE id_medecin=:id";
        $stmt=$linkpdo->prepare($query);
        $stmt->bindParam(':id',$id);
        $stmt->execute();
        $linkpdo=null;
        return true;
    }catch(PDOException $e){
        return $e;
    }
}
function updateMedecin($medecin){
    try{
        $linkpdo=Connexion::getInstance();
        $query="UPDATE medecin SET nom=:nom,prenom=:prenom,civilite=:civilite WHERE id_medecin=:id";
        $stmt=$linkpdo->prepare($query);
        $stmt->bindParam(':id',$medecin['id']);
        $stmt->bindParam(':nom',$medecin['nom']);
        $stmt->bindParam(':prenom',$medecin['prenom']);
        $stmt->bindParam(':civilite',$medecin['civilite']);
        $stmt->execute();
        $linkpdo=null;
        $medecin=getMedecinById($medecin['id']);
        return $medecin;
    }catch(PDOException $e){
        return $e;
    }
}
function getMedecinByNom($nom){
    try{
        $linkpdo=Connexion::getInstance();
        $query="SELECT * FROM medecin WHERE nom=:nom";
        $stmt=$linkpdo->prepare($query);
        $stmt->bindParam(':nom',$nom);
        $stmt->execute();
        $result=$stmt->fetchAll();
        $medecins=array();
        foreach($result as $row){
            $medecin=array(
                "id"=>$row['id_medecin'],
                "nom"=>$row['nom'],
                "prenom"=>$row['prenom'],
                "specialite"=>$row['civilite']
            );
            array_push($medecins,$medecin);
        }
        $linkpdo=null;
        return $medecins;
    }catch(PDOException $e){
        return $e;
    }
}
?>