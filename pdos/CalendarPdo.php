<?php

function getCalendar($kakaoId, $date){
    $pdo = pdoSqlConnect();

    $query = "SELECT distinct (scheduleDate)
                FROM PLANS_TB
               WHERE kakaoId = ? AND scheduleDate LIKE '$date%'
               ORDER BY scheduleDate ASC";

    $st = $pdo->prepare($query);
    $st->execute([$kakaoId]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;

    return $res;
}