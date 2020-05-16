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