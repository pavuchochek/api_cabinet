<?php
require_once('Connexion.php');
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
                "civilite"=>$row['civilite']
            );
            array_push($medecins,$medecin);
        }
        $linkpdo=null;
        return $medecins;
    }catch(PDOException $e){
        $result=array("error"=>$e->errorInfo[1],"info"=>"Erreur SQL, contactez l'administrateur");
        return $result;
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
        $result=array("error"=>$e->errorInfo[1]);
        $result["info"]="Erreur SQL, contactez l'administrateur";
        return $result;

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
        $result=array("error"=>$e->errorInfo[1]);
        $result["info"]="Erreur SQL, contactez l'administrateur";
        if($e->errorInfo[1]==1406){
            $result['info']="Erreur de longueur de champs";
        }
        if($e->errorInfo[1]==1366){
            $result['info']="Erreur de type de champs";
        }
        return $result;
    }
}
function deleteMedecin($id){
    try{
        $linkpdo=Connexion::getInstance();
        //Suppression du medecin en tant que Medecin Referent
        $query1="UPDATE usager SET id_medecin=NULL WHERE id_medecin=:id";
        $stmt1=$linkpdo->prepare($query1);
        $stmt1->bindParam(':id',$id);
        $stmt1->execute();
        //Suppression des consultations du medecin
        $query2="DELETE FROM consultation WHERE id_medecin=:id";
        $stmt2=$linkpdo->prepare($query2);
        $stmt2->bindParam(':id',$id);
        $stmt2->execute();
        //Suppression du medecin
        $query="DELETE FROM medecin WHERE id_medecin=:id";
        $stmt=$linkpdo->prepare($query);
        $stmt->bindParam(':id',$id);
        $stmt->execute();
        $linkpdo=null;
        return true;
    }catch(PDOException $e){
        $result=array("error"=>$e->errorInfo[1]);
        $result["info"]="Erreur SQL, contactez l'administrateur";
        return $result;
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
        $result=array("error"=>$e->errorInfo[1]);
        $result["info"]="Erreur SQL, contactez l'administrateur";
        if($e->errorInfo[1]==1406){
            $result['info']="Erreur de longueur de champs";
        }
        if($e->errorInfo[1]==1366){
            $result['info']="Erreur de type de champs";
        }
        return $result;
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
                "civilite"=>$row['civilite']
            );
            array_push($medecins,$medecin);
        }
        $linkpdo=null;
        return $medecins;
    }catch(PDOException $e){
        $result=array("error"=>$e->errorInfo[1]);
        $result["info"]="Erreur SQL, contactez l'administrateur";
        return $result;
    }
}
?>