<?php

function posts($kakaoId, $lastNo){
    $pdo = pdoSqlConnect();
    $query = "";
    if(!$lastNo){
        $query = "SELECT PT.postId, PT.title, PT.postContents, PT.createdAt, ifnull(CT.cnt, 0) AS cnt, ifnull(FT.isPriority, 0) AS isFavorite
                    FROM POST_TB AS PT
                    LEFT JOIN (SELECT postId, count(*) AS cnt
                                 FROM COMMENT_TB
                                GROUP BY postId) AS CT ON CT.postId = PT.postId
                                 LEFT JOIN (SELECT postId, isPriority
                                              FROM FAVORPOST_TB
                                             WHERE kakaoId = ?) FT on PT.postId = FT.postId
                   ORDER BY PT.postId DESC LIMIT 0, 20;";

        $st = $pdo->prepare($query);
        $st->execute([$kakaoId]);
    } else {
        $query = "SELECT PT.postId, PT.title, PT.postContents, PT.createdAt, ifnull(CT.cnt, 0) AS cnt, ifnull(FT.isPriority, 0) AS isFavorite
                    FROM POST_TB AS PT
                    LEFT JOIN (SELECT postId, count(*) AS cnt
                                 FROM COMMENT_TB
                                GROUP BY postId) AS CT ON CT.postId = PT.postId
                                 LEFT JOIN (SELECT postId, isPriority
                                              FROM FAVORPOST_TB
                                             WHERE kakaoId = ?) FT on PT.postId = FT.postId
                    WHERE PT.postId < ?
                   ORDER BY PT.postId DESC LIMIT 0, 20";


        $st = $pdo->prepare($query);
        $st->execute([$kakaoId, $lastNo]);
    }

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function createPost($kakaoId, $title, $postContents)
{
    $pdo = pdoSqlConnect();

    $query = "INSERT INTO POST_TB (writerId, title, postContents) 
                   VALUES (?, ?, ?);";

    $st = $pdo->prepare($query);
    $st->execute([$kakaoId, $title, $postContents]);

    $st = null;
    $pdo = null;

}

function modifyPost($kakaoId, $postId, $title, $postContents)
{

    $pdo = pdoSqlConnect();

    $query = "UPDATE POST_TB
                 SET title = ?, postContents = ?
               WHERE writerId = ? AND postId = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$title, $postContents, $kakaoId, $postId]);

    $st = null;
    $pdo = null;

}

function deletePost($kakaoId, $postId)
{

    $pdo = pdoSqlConnect();

    $query = "DELETE FROM POST_TB
               WHERE writerId = ? 
                 AND postId = ?";

    $st = $pdo->prepare($query);
    $st->execute([$kakaoId, $postId]);

    $st = null;
    $pdo = null;

}

function basicPosts($kakaoId, $sort){
    $pdo = pdoSqlConnect();
    if(!$sort){
        $query = "SELECT PT.postId, PT.title, PT.postContents, PT.createdAt, ifnull(CT.cnt, 0) AS cnt, ifnull(FT.isPriority, 0) AS isFavorite
                FROM POST_TB AS PT
                LEFT JOIN (SELECT postId, count(*) AS cnt
                             FROM COMMENT_TB
                            GROUP BY postId) AS CT ON CT.postId = PT.postId
                LEFT JOIN (SELECT postId, isPriority
                             FROM FAVORPOST_TB
                            WHERE kakaoId = ?) FT on PT.postId = FT.postId
               WHERE PT.createdAt BETWEEN DATE_ADD(NOW(),INTERVAL -1 WEEK ) AND NOW()
               ORDER BY cnt DESC LIMIT 0, 5";

        $st = $pdo->prepare($query);
        $st->execute([$kakaoId]);
    } else if($sort == "mine"){
        $query = "SELECT PT.postId, PT.title, PT.postContents, PT.createdAt, ifnull(CT.cnt, 0) AS cnt, ifnull(FT.isPriority, 0) AS isFavorite
                    FROM POST_TB AS PT
                    LEFT JOIN (SELECT postId, count(*) AS cnt
                                FROM COMMENT_TB
                               GROUP BY postId) AS CT ON CT.postId = PT.postId
                    LEFT JOIN (SELECT postId, isPriority
                                FROM FAVORPOST_TB
                               WHERE kakaoId = ?) FT on PT.postId = FT.postId
                   WHERE PT.writerId = ?
                   ORDER BY PT.postId DESC";

        $st = $pdo->prepare($query);
        $st->execute([$kakaoId, $kakaoId]);
    } else if($sort == "mycomment"){
        $query = "SELECT CT.postId, CT.emotionId, PT.title, PT.postContents, PT.createdAt,ifnull(CNTCT.cnt, 0) AS cnt, ifnull(FT.isPriority, 0) AS isFavorite
                    FROM COMMENT_TB AS CT
                    LEFT JOIN (SELECT postId, isPriority
                                FROM FAVORPOST_TB
                               WHERE kakaoId = ?) FT on CT.postId = FT.postId
                    LEFT JOIN POST_TB AS PT on PT.postId = CT.postId
                    LEFT JOIN (SELECT postId, count(*) AS cnt
                                 FROM COMMENT_TB
                                GROUP BY postId) CNTCT on CNTCT.postId = CT.postId
                   WHERE commenterId = ?
                   ORDER BY CT.postId DESC";

        $st = $pdo->prepare($query);
        $st->execute([$kakaoId, $kakaoId]);
    } else if($sort == favorite){
        $query = "SELECT PT.postId, PT.title, PT.postContents, PT.createdAt, ifnull(CT.cnt, 0) AS cnt, ifnull(FT.isPriority, 0) AS isFavorite
                    FROM POST_TB AS PT
                    LEFT JOIN (SELECT postId, count(*) AS cnt
                                FROM COMMENT_TB
                               GROUP BY postId) AS CT ON CT.postId = PT.postId
                    LEFT JOIN (SELECT postId, isPriority
                                FROM FAVORPOST_TB
                               WHERE kakaoId = ?) FT on PT.postId = FT.postId
                   WHERE FT.isPriority = 1
                  ORDER BY PT.postId DESC";

        $st = $pdo->prepare($query);
        $st->execute([$kakaoId]);
    }
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function detailPost($kakaoId, $postId){
    $pdo = pdoSqlConnect();
    $query = "SELECT PT.postId, PT.title, PT.postContents, PT.createdAt, ifnull(FT.isPriority, 0) AS isFavorite
                FROM POST_TB AS PT
                LEFT JOIN (SELECT postId, isPriority
                             FROM FAVORPOST_TB
                            WHERE kakaoId = ?) FT on PT.postId = FT.postId
               WHERE PT.postId = ?";

    $st = $pdo->prepare($query);
    $st->execute([$kakaoId, $postId]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $secondQuery = "SELECT ifnull(emotionId, 0) AS emotionId
                      FROM POST_TB AS PT
                      LEFT JOIN (SELECT CT.postId , CT.commenterId, ET.emotionId
                              FROM EMOTION_TB AS ET
                              LEFT JOIN COMMENT_TB AS CT on CT.emotionId = ET.emotionId
                             WHERE commenterId = ?) A on A.postId = PT.postId
                     WHERE PT.postId = ?";
    $st = $pdo->prepare($secondQuery);
    $st->execute([$kakaoId, $postId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $secondRes = $st->fetchAll();

    $res[0]["myEmotionId"] = $secondRes[0]["emotionId"];

    $thirdQuery = "SELECT ET.emotionId, ifnull(CT.cnt, 0) AS cnt
                      FROM EMOTION_TB AS ET
                      LEFT JOIN( SELECT emotionId, count(*) AS cnt
                                   FROM COMMENT_TB
                                  WHERE postId = ?
                                  GROUP BY emotionId
                              ) CT on CT.emotionId = ET.emotionId
                     WHERE ET.emotionId > 0";
    $st = $pdo->prepare($thirdQuery);
    $st->execute([$postId]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $thirdRes = $st->fetchAll();
    for ($i=0; $i<6; $i++){
        $res[0]["leaveEmotion"][$i] = $thirdRes[$i];
    }

    $st = null;
    $pdo = null;

    return $res[0];
}