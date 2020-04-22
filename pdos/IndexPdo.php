<?php

function isValidUser($kakaoId){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM USER_TB WHERE kakaoId= ?) AS exist;";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$kakaoId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);

}