<?php

function isPlan($kakaoId, $no){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(
                            SELECT *
                              FROM PLANS_TB
                             WHERE kakaoId= ? AND no =?) AS exist";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$kakaoId, $no]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);

}

function isFavorite($kakaoId, $no){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(
                            SELECT * 
                              FROM PLANS_TB 
                             WHERE kakaoId= ? AND no = ?
                               AND isPriority = 0) AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$kakaoId, $no]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]["exist"]);
}

function isPost($kakaoId, $postId){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(
                            SELECT *
                              FROM POST_TB
                             WHERE writerId= ? AND postId =?) AS exist";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$kakaoId, $postId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);

}
