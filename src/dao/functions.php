<?php
function createChuckFact($linkpdo,$phrase){
    try{
        $date=date('Y-m-d H:i:s');
        $res = $linkpdo->prepare('INSERT INTO chuckn_facts(phrase,date_ajout,date_modif,vote,faute,signalement) VALUES (:phrase,:date_ajout,:date_modif,:faute,:signalement,:vote)');
    $varZero=0;
    $res->bindParam(':phrase',$phrase,PDO::PARAM_STR);
    $res->bindParam(':date_ajout',$date,PDO::PARAM_STR);
    $res->bindParam(':date_modif',$date,PDO::PARAM_STR);
    $res->bindParam(':faute',$varZero,PDO::PARAM_INT);
    $res->bindParam(':signalement',$varZero,PDO::PARAM_INT);
    $res->bindParam(':vote',$varZero,PDO::PARAM_INT);
 
    $linkpdo->beginTransaction();
    $res->execute();
    $id=$linkpdo->lastInsertId();
    $linkpdo->commit();
    $data=readChuckFactId($linkpdo,$id);
    return $data;
    }catch(PDOException $e){
        return $e;
    }
    
}
function deleteChuckFact($linkpdo,$id){
    if($id>44){
        try{
            $res = $linkpdo->prepare('DELETE FROM chuckn_facts WHERE id = :idA');
            $res->bindParam(':idA',$id,PDO::PARAM_INT);
            $linkpdo->beginTransaction();
            $res->execute();
            $linkpdo->commit();
        }catch(PDOException $e){
            return $e;
        }
    }
    
}

function deliver_response($status,$status_code, $status_message, $data=null,$options=null){
    /// Paramétrage de l'entête HTTP
    http_response_code($status_code); //Utilise un message standardisé en
    if($options){
        header("Access-Control-Allow-Methods: *");
        header("Access-Control-Allow-Headers: *");
    }
    header("HTTP/1.1 $status_code $status_message"); 
    header("Content-Type:application/json; charset=utf-8");
    header("Access-Control-Allow-Origin: *");
    $response['status']=$status;
    $response['status_code'] = $status_code;
    $response['status_message'] = $status_message;
    if($data){
        $response['data'] = $data;
    }
    /// Mapping de la réponse au format JSON
    $json_response = json_encode($response);
    if($json_response===false)
    die('json encode ERROR : '.json_last_error_msg());
    /// Affichage de la réponse (Retourné au client)
    echo $json_response;
    }
function getInfoUserFromToken($linkpdo,$token){
    $payload=getInfoFromToken($token);
    $login=$payload['login'];
    $query = "SELECT * FROM user WHERE login = :login";
    $stmt = $linkpdo->prepare($query);
    $stmt->bindParam(':login', $login);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result['role'];
}
function voteup($linkpdo,$id){
    try{
        $res = $linkpdo->prepare('UPDATE chuckn_facts set vote = vote + 1 WHERE id= :id');
        $res->bindParam(':id',$id,PDO::PARAM_INT);
        $res->execute();
        $data=readChuckFactId($linkpdo,$id);
        return $data;
        }catch(PDOException $e){
            return null;
        }
}
function voteDown($linkpdo,$id){
    try{
        $res = $linkpdo->prepare('UPDATE chuckn_facts set vote = vote - 1 WHERE id= :id');
        $res->bindParam(':id',$id,PDO::PARAM_INT);
        $res->execute();
        $data=readChuckFactId($linkpdo,$id);
        return $data;
        }catch(PDOException $e){
            return null;
        }
}
function signal($linkpdo,$id){
    try{
        $res = $linkpdo->prepare('UPDATE chuckn_facts set signalement = 1 WHERE id= :id');
        $res->bindParam(':id',$id,PDO::PARAM_INT);
        $res->execute();
        $data=readChuckFactId($linkpdo,$id);
        return $data;
        }catch(PDOException $e){
            return null;
        }
}

function notSignal($linkpdo,$id){
    try{
        $res = $linkpdo->prepare('UPDATE chuckn_facts set signalement = 0 WHERE id= :id');
        $res->bindParam(':id',$id,PDO::PARAM_INT);
        $res->execute();
        $data=readChuckFactId($linkpdo,$id);
        return $data;
        }catch(PDOException $e){
            return null;
        }
}
function readChuckFact($linkpdo){
    $res = $linkpdo->query('SELECT * FROM chuckn_facts');
    $data[] = $res->fetchAll(PDO::FETCH_ASSOC);
    return $data;
}
function readChuckFactId($linkpdo,$id){
    $res = $linkpdo->prepare('SELECT * FROM chuckn_facts WHERE id = :idA');
    $res->bindParam(':idA',$id,PDO::PARAM_INT); //Attention au type du paramètre !
    $res->execute();
    return $res->fetchAll(PDO::FETCH_ASSOC);
}
function getAllSignaled($linkpdo){
    $res = $linkpdo->prepare('SELECT * FROM chuckn_facts WHERE signalement=1');
    $res->execute();
    return $res->fetchAll(PDO::FETCH_ASSOC);
}
function update($linkpdo,$id,$data){
    if($id>45){
        try{
        $date=date('Y-m-d H:i:s');
        $res = $linkpdo->prepare('UPDATE chuckn_facts set phrase = :phrase, date_modif = :date_modif,faute = :faute,signalement = :signalement,vote = :vote WHERE id= :id');
        $res->bindParam(':id',$id,PDO::PARAM_INT);
        $res->bindParam(':phrase',$data["phrase"],PDO::PARAM_STR);
        $res->bindParam(':date_modif',$date,PDO::PARAM_STR);
        $res->bindParam(':faute',$data["faute"],PDO::PARAM_INT);
        $res->bindParam(':signalement',$data["signalement"],PDO::PARAM_INT);
        $res->bindParam(':vote',$data["vote"],PDO::PARAM_INT);
        $res->execute();
        $data=readChuckFactId($linkpdo,$id);
        return $data;
        }catch(PDOException $e){
            return $e;
        }
    }}
function getLastNPhrases($linkpdo,$nombre){
    $res = $linkpdo->prepare('SELECT * FROM chuckn_facts ORDER BY date_ajout DESC LIMIT :nombre');
    $res->bindParam(':nombre', $nombre, PDO::PARAM_INT);
    $res->execute(); // Exécuter la requête après avoir lié les paramètres
    $data = $res->fetchAll(PDO::FETCH_ASSOC);
    return $data;
}
function getBestPhrases($linkpdo,$nombre){
    $res = $linkpdo->prepare('SELECT * FROM chuckn_facts ORDER BY vote DESC LIMIT :nombre');
    $res->bindParam(':nombre', $nombre, PDO::PARAM_INT);
    $res->execute(); // Exécuter la requête après avoir lié les paramètres
    $data = $res->fetchAll(PDO::FETCH_ASSOC);
    return $data;
}   
?>