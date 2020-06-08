<?php
function updateFcm($kakaoId, $fcmToken)
{
    $pdo = pdoSqlConnect();

    $query = "UPDATE USER_TB
                 SET fcmToken = ?
               WHERE kakaoId = ?";

    $st = $pdo->prepare($query);
    $st->execute([$fcmToken, $kakaoId]);

    $st = null;
    $pdo = null;

}

function getFcmTokenByPost($postId){
    $pdo = pdoSqlConnect();

    $query = "SELECT UT.fcmToken
                FROM USER_TB UT
                LEFT JOIN POST_TB PT on UT.kakaoId = PT.writerId
               WHERE PT.postId = ?
               LIMIT 1";

    $st = $pdo->prepare($query);
    $st->execute([$postId]);

    $st->setFetchMode(PDO::FETCH_ASSOC);

    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["fcmToken"];
}

function getPostTitle($postId){
    $pdo = pdoSqlConnect();

    $query = "SELECT title
                FROM POST_TB
               WHERE postId = ?";

    $st = $pdo->prepare($query);
    $st->execute([$postId]);

    $st->setFetchMode(PDO::FETCH_ASSOC);

    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["title"];
}