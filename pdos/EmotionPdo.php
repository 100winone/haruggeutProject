<?php

function modifyEmotion($kakaoId, $no, $emotionId)
{

    $pdo = pdoSqlConnect();

    $query = "UPDATE PLANS_TB
                 SET emotionId = ?
               WHERE kakaoId = ? AND no = ?";

    $st = $pdo->prepare($query);
    $st->execute([$emotionId, $kakaoId, $no]);

    $st = null;
    $pdo = null;
}

function createComment($commenterId, $postId, $emotionId)
{
    $pdo = pdoSqlConnect();

    $query = "INSERT INTO COMMENT_TB (postId, commenterId, emotionId) 
                   VALUES (?, ?, ?);";

    $st = $pdo->prepare($query);
    $st->execute([$postId, $commenterId, $emotionId]);

    $st = null;
    $pdo = null;

}

function modifyComment($commenterId, $postId, $emotionId)
{

    $pdo = pdoSqlConnect();

    $query = "UPDATE COMMENT_TB
                 SET emotionId = ?
               WHERE commenterId = ? AND postId = ?";

    $st = $pdo->prepare($query);
    $st->execute([$emotionId, $commenterId, $postId]);

    $st = null;
    $pdo = null;
}

