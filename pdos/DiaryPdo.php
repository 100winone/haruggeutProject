<?php

function diary($kakaoId, $date){
    $pdo = pdoSqlConnect();
    $query = "";
    if($date == null){
        $query = "SELECT emotionId, COUNT(*) AS CNT, scheduleDate
                    FROM PLANS_TB
                   WHERE kakaoId = ? AND scheduleDate = DATE(NOW()) AND emotionId > 0
                   GROUP BY emotionId
                   ORDER BY CNT DESC LIMIT 1";

        $st = $pdo->prepare($query);
        $st->execute([$kakaoId]);
    }
    else if ($date != null){
        $query = "SELECT emotionId, COUNT(*) AS CNT, scheduleDate
                    FROM PLANS_TB
                   WHERE kakaoId = ? AND scheduleDate = ? AND emotionId > 0
                   GROUP BY emotionId
                   ORDER BY CNT DESC LIMIT 1";

        $st = $pdo->prepare($query);
        $st->execute([$kakaoId, $date]);
    }

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $emotionId =  $res[0]["emotionId"];
    $scheduleDate = $res[0]["scheduleDate"];

    $lastQuery = "SELECT ST.emotionId AS emotionId, ST.storyTitle, ST.storyContents, PT.title, PT.contents, PT.startTime
                FROM STORY_TB AS ST
                LEFT JOIN PLANS_TB PT on ST.emotionId = PT.emotionId
               WHERE ST.emotionId = ? AND PT.scheduleDate = ? AND PT.kakaoId = ?
               ORDER BY RAND() LIMIT 1";

    $st = $pdo->prepare($lastQuery);
    $st->execute([$emotionId, $scheduleDate, $kakaoId]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;

    return $res[0];
}