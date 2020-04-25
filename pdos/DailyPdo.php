<?php

function createPlan($kakaoId, $colorId, $place, $contents, $startTime, $endTime, $scheduleDate, $title)
{

    $pdo = pdoSqlConnect();

    $query = "INSERT INTO PLANS_TB (kakaoId, colorId, place, contents, startTime, endTime, scheduleDate, title)
              VALUES(?, ?, ?, ?, ?, ?, ?, ?)";

    $st = $pdo->prepare($query);
    $st->execute([$kakaoId, $colorId, $place, $contents, $startTime, $endTime, $scheduleDate, $title]);

    $st = null;
    $pdo = null;

}

function plans($kakaoId, $date){
    $pdo = pdoSqlConnect();
    $query = "";
    if($date == null){
        $query = "SELECT @ROWNUM := @ROWNUM + 1 AS sequence, A.* FROM
        (SELECT no AS planNo, title, colorId, emotionId, isPriority, startTime, endTime, place, scheduleDate
           FROM PLANS_TB
          WHERE scheduleDate = DATE(NOW()) AND kakaoId = ?
          ORDER BY startTime ASC) A,
        (SELECT @ROWNUM := 0 ) B";

        $st = $pdo->prepare($query);
        $st->execute([$kakaoId]);
    }
    else if ($date != null){
        $query = "SELECT @ROWNUM := @ROWNUM + 1 AS sequence, A.* FROM
        (SELECT no AS planNo, title, colorId, emotionId, isPriority, startTime, endTime, place, scheduleDate
           FROM PLANS_TB
          WHERE scheduleDate = ? AND kakaoId = ?
          ORDER BY startTime ASC) A,
        (SELECT @ROWNUM := 0 ) B";

        $st = $pdo->prepare($query);
        $st->execute([$date, $kakaoId]);
    }

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function deletePlan($kakaoId, $no)
{

    $pdo = pdoSqlConnect();

    $query = "DELETE FROM PLANS_TB
               WHERE kakaoId = ? 
                 AND no = ?";

    $st = $pdo->prepare($query);
    $st->execute([$kakaoId, $no]);

    $st = null;
    $pdo = null;

}