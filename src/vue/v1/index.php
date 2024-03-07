<?php

include('../../dao/Connexion.php');
include('../../dao/functions.php');
$https_method=$_SERVER['REQUEST_METHOD'];
$linkpdo=Connexion::getInstance();
switch($https_method){
    case "GET":
        if(isset($_GET['id'])){
            $id=$_GET['id'];
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
                deliver_response("OK",200,"Succes",$medecin);
            }else{
                deliver_response("Error",404,"Not Found");
            }
        }else{
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
            deliver_response("OK",200,"Succes",$medecins);
        }
}

?>