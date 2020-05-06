<?php

function posts($kakaoId, $sort){
    $pdo = pdoSqlConnect();
    $query = "";

    $query = "SELECT *
                FROM POST_TB
               WHERE writerId = ?
               ORDER BY createdAt DESC";

    $st = $pdo->prepare($query);
    $st->execute([$kakaoId]);


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