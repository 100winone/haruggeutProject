<?php

function createPlan($kakaoId, $colorId, $place, $contents, $startTime, $endTime, $scheduleDate, $title, $isPriority)
{

    $pdo = pdoSqlConnect();
    if(!$isPriority)
        $isPriority = 0;
    $query = "INSERT INTO PLANS_TB (kakaoId, colorId, place, contents, startTime, endTime, scheduleDate, title, isPriority)
              VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $st = $pdo->prepare($query);
    $st->execute([$kakaoId, $colorId, $place, $contents, $startTime, $endTime, $scheduleDate, $title, $isPriority]);

    $st = null;
    $pdo = null;

}

function plans($kakaoId, $date){
    $pdo = pdoSqlConnect();
    $query = "";
    if($date == null){
        $query = "SELECT @ROWNUM := @ROWNUM + 1 AS sequence, A.* FROM
        (SELECT no AS planNo, title, colorId, emotionId, isPriority, startTime, endTime, place, scheduleDate, contents
           FROM PLANS_TB
          WHERE scheduleDate = DATE(NOW()) AND kakaoId = ?
          ORDER BY startTime ASC) A,
        (SELECT @ROWNUM := 0 ) B";

        $st = $pdo->prepare($query);
        $st->execute([$kakaoId]);
    }
    else if ($date != null){
        $query = "SELECT @ROWNUM := @ROWNUM + 1 AS sequence, A.* FROM
        (SELECT no AS planNo, title, colorId, emotionId, isPriority, startTime, endTime, place, scheduleDate, contents
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

function detailPlan($planNo ,$kakaoId){
    $pdo = pdoSqlConnect();

    $query = "SELECT no AS planNo, colorId, place, title, contents, startTime, endTime, scheduleDate, isPriority, emotionId
                FROM PLANS_TB
               WHERE no = ? and kakaoId = ?";

    $st = $pdo->prepare($query);
    $st->execute([$planNo, $kakaoId]);

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

function updateFavoriteStatus($kakaoId, $no)
{

    $pdo = pdoSqlConnect();

    $query = "UPDATE PLANS_TB
                 SET isPriority = CASE WHEN isPriority = 1 THEN 0
                                    WHEN isPriority = 0 THEN 1
                                     END
               WHERE kakaoId = ? AND no = ?";

    $st = $pdo->prepare($query);
    $st->execute([$kakaoId, $no]);

    $st = null;
    $pdo = null;

}

function editPlan($kakaoId, $no, $colorId, $place, $contents, $startTime, $endTime, $scheduleDate, $title, $isPriority)
{

    $pdo = pdoSqlConnect();

    $query = "UPDATE PLANS_TB
                 SET colorId = ?, place = ?, contents = ?, startTime = ?, endTime = ?, scheduleDate = ?, title = ?, isPriority = ?
               WHERE kakaoId = ? AND no = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$colorId, $place, $contents, $startTime, $endTime, $scheduleDate, $title, $isPriority, $kakaoId, $no]);

    $st = null;
    $pdo = null;

}
