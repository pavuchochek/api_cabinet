<?php
require_once('Connexion.php');
function getUsagers(){
    try{
        $linkpdo=Connexion::getInstance();
        $query="SELECT * FROM usager";
        $stmt=$linkpdo->prepare($query);
        $stmt->execute();
        $result=$stmt->fetchAll();
        $usagers=array();
        foreach($result as $row){
            $usager=array(
                "id"=>$row['id_usager'],
                "civilite"=>$row['civilite'],
                "nom"=>$row['nom'],
                "prenom"=>$row['prenom'],
                "sexe"=>$row['sexe'],
                "adresse"=>$row['adresse'],
                "code_postal"=>$row['code_postal'],
                "ville"=>$row['ville'],
                "date_nais"=>$row['date_nais'],
                "lieu_nais"=>$row['lieu_nais'],
                "num_secu"=>$row['num_secu'],
                "id_medecin"=>$row['id_medecin']
            );
            array_push($usagers,$usager);
        }
        $linkpdo=null;
        return $usagers;
    }
    catch(PDOException $e){
        $res=Array();
        $res['error']=$e->errorInfo[1];
        $res['info']="Erreur SQL, contactez l'administrateur";
        return $res;
    }
}
function getUsagerById($id){
    try{
        $linkpdo=Connexion::getInstance();
        $query="SELECT * FROM usager WHERE id_usager=:id";
        $stmt=$linkpdo->prepare($query);
        $stmt->bindParam(':id',$id);
        $stmt->execute();
        $result=$stmt->fetch();
        if($result){
            $usager=array(
                "id"=>$result['id_usager'],
                "civilite"=>$result['civilite'],
                "nom"=>$result['nom'],
                "prenom"=>$result['prenom'],
                "sexe"=>$result['sexe'],
                "adresse"=>$result['adresse'],
                "code_postal"=>$result['code_postal'],
                "ville"=>$result['ville'],
                "date_nais"=>$result['date_nais'],
                "lieu_nais"=>$result['lieu_nais'],
                "num_secu"=>$result['num_secu'],
                "id_medecin"=>$result['id_medecin']
            );
            $linkpdo=null;
            return $usager;
        }else{
            $linkpdo=null;
            return null;
        }
    }catch(PDOException $e){
        $res=Array();
        $res['error']=$e->errorInfo[1];
        $res['info']="Erreur SQL, contactez l'administrateur";
        return $res;
    }
}
function deleteUsager($id){
    try{
        $linkpdo=Connexion::getInstance();
        //Suppression des consultations liées à l'usager
        $query="DELETE FROM consultation WHERE id_usager=:id";
        $stmt=$linkpdo->prepare($query);
        $stmt->bindParam(':id',$id);
        $stmt->execute();
        // Suppression de l'usager
        $query="DELETE FROM usager WHERE id_usager=:id";
        $stmt=$linkpdo->prepare($query);
        $stmt->bindParam(':id',$id);
        $stmt->execute();
        $linkpdo=null;
        return true;
    }catch(PDOException $e){
        $res["error"]=$e->errorInfo[1];
        $res["info"]="Erreur SQL, contactez l'administrateur";
        return $res;
    }
}
function addUsager($usager){
    try{
        $linkpdo=Connexion::getInstance();
        $query="INSERT INTO usager(civilite,nom,prenom,sexe,adresse,code_postal,ville,date_nais,lieu_nais,num_secu,id_medecin) VALUES(:civilite,:nom,:prenom,:sexe,:adresse,:code_postal,:ville,:date_nais,:lieu_nais,:num_secu,:id_medecin)";
        $stmt=$linkpdo->prepare($query);
        $stmt->bindParam(':civilite',$usager['civilite']);
        $stmt->bindParam(':nom',$usager['nom']);
        $stmt->bindParam(':prenom',$usager['prenom']);
        $stmt->bindParam(':sexe',$usager['sexe']);
        $stmt->bindParam(':adresse',$usager['adresse']);
        $stmt->bindParam(':code_postal',$usager['code_postal']);
        $stmt->bindParam(':ville',$usager['ville']);
        $stmt->bindParam(':date_nais',$usager['date_nais']);
        $stmt->bindParam(':lieu_nais',$usager['lieu_nais']);
        $stmt->bindParam(':num_secu',$usager['num_secu']);
        $stmt->bindParam(':id_medecin',$usager['id_medecin']);
        $stmt->execute();
        $id=$linkpdo->lastInsertId();
        $usagerInsere=getUsagerById($id);
        $linkpdo=null;
        return $usagerInsere;
    }catch(PDOException $e){
        $res=Array();
        $res['error']=$e->errorInfo[1];
        $res['info']="Erreur SQL, contactez l'administrateur";
        if($e->errorInfo[1]==1062){
            $res['info']="Erreur d'unicité, ce numero de sécurité sociale est déjà attribué";
        }
        if($e->errorInfo[1]==1406){
            $res['info']="Erreur de longueur de champs";
        }
        if($e->errorInfo[1]==1366){
            $res['info']="Erreur de type de champs";
        }
        return $res;
    }
}
function updateUsager($usager){
    try{
        $linkpdo=Connexion::getInstance();
        $query="UPDATE usager SET civilite=:civilite,nom=:nom,prenom=:prenom,sexe=:sexe,adresse=:adresse,code_postal=:code_postal,ville=:ville,date_nais=:date_nais,lieu_nais=:lieu_nais,num_secu=:num_secu,id_medecin=:id_medecin WHERE id_usager=:id";
        $stmt=$linkpdo->prepare($query);
        $stmt->bindParam(':id',$usager['id']);
        $stmt->bindParam(':civilite',$usager['civilite']);
        $stmt->bindParam(':nom',$usager['nom']);
        $stmt->bindParam(':prenom',$usager['prenom']);
        $stmt->bindParam(':sexe',$usager['sexe']);
        $stmt->bindParam(':adresse',$usager['adresse']);
        $stmt->bindParam(':code_postal',$usager['code_postal']);
        $stmt->bindParam(':ville',$usager['ville']);
        $stmt->bindParam(':date_nais',$usager['date_nais']);
        $stmt->bindParam(':lieu_nais',$usager['lieu_nais']);
        $stmt->bindParam(':num_secu',$usager['num_secu']);
        $stmt->bindParam(':id_medecin',$usager['id_medecin']);
        $stmt->execute();
        $usagerModifie=getUsagerById($usager['id']);
        $linkpdo=null;
        return $usagerModifie;
    }catch(PDOException $e){
        $res["error"]=$e->errorInfo[1];
        $res["info"]="Veuillez contacter l'administrateur";
        if($e->errorInfo[1]==1406){
            $res['info']="Erreur de longueur de champs";
        }
        if($e->errorInfo[1]==1366){
            $res['info']="Erreur de type de champs";
        }
        if($e->errorInfo[1]==1062){
            $res['info']="Erreur d'unicité, ce numero de sécurité sociale est déjà attribué";
        }
        return $res;
    }
}