<?php

function foryou($kakaoId){
    $pdo = pdoSqlConnect();

    $firstQuery = "SELECT title, scheduleDate, place, emotionId
                     FROM(SELECT kakaoId , title, scheduleDate, place, emotionId
                           FROM PLANS_TB
                          WHERE kakaoId = ? AND scheduleDate > date_add(now(),interval -7 day) AND emotionId = 1
                          ORDER BY RAND() LIMIT 1) AS A
                    UNION
                    SELECT title, scheduleDate, place, emotionId
                      FROM(SELECT kakaoId , title, scheduleDate, place, emotionId
                             FROM PLANS_TB
                             WHERE kakaoId = ? AND scheduleDate > date_add(now(),interval -7 day) AND emotionId = 3
                             ORDER BY RAND() LIMIT 1) AS B           
                    UNION
                    SELECT title, scheduleDate, place, emotionId
                      FROM(SELECT kakaoId , title, scheduleDate, place, emotionId
                             FROM PLANS_TB
                            WHERE kakaoId = ? AND scheduleDate > date_add(now(),interval -7 day) AND emotionId = 4
                           ORDER BY RAND() LIMIT 1) AS C";

    $st = $pdo->prepare($firstQuery);
    $st->execute([$kakaoId, $kakaoId, $kakaoId]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}