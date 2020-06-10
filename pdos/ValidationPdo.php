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

function isMyPost($kakaoId, $postId){
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

function isPost($postId){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(
                            SELECT *
                              FROM POST_TB
                             WHERE postId =?) AS exist";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$postId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);

}

function isCommented($commenterId, $postId){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(
                            SELECT *
                              FROM COMMENT_TB
                             WHERE commenterId = ? AND postId =?) AS exist";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$commenterId, $postId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);

}

function isLastPost($kakaoId, $lastNo){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(
                            SELECT PT.postId, PT.title, PT.postContents, PT.createdAt, ifnull(CT.cnt, 0) AS cnt, ifnull(FT.isPriority, 0) AS isFavorite
                    FROM POST_TB AS PT
                    LEFT JOIN (SELECT postId, count(*) AS cnt
                                 FROM COMMENT_TB
                                GROUP BY postId) AS CT ON CT.postId = PT.postId
                                 LEFT JOIN (SELECT postId, isPriority
                                              FROM FAVORPOST_TB
                                             WHERE kakaoId = ?) FT on PT.postId = FT.postId
                    WHERE PT.postId < ?) AS exist";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$kakaoId, $lastNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function isAlreadyFavorite($kakaoId, $postId){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(
                            SELECT *
                              FROM FAVORPOST_TB
                             WHERE kakaoId = ? AND postId = ?) AS exist";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$kakaoId, $postId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);

}

function favoriteState($kakaoId, $postId){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(
                            SELECT *
                              FROM FAVORPOST_TB
                             WHERE kakaoId= ? AND postId = ?
                               AND isPriority = 0) AS exist";

    $st = $pdo->prepare($query);
    $st->execute([$kakaoId, $postId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]["exist"]);
}
