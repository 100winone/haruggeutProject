<?php

function boards(){
    $pdo = pdoSqlConnect();


    $query = "SELECT boardId, boardName
                FROM BOARD_TB";

    $st = $pdo->prepare($query);
    $st->execute();


    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}