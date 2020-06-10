<?php

function createUser($kakaoId)
{
    $pdo = pdoSqlConnect();

    $query = "INSERT INTO USER_TB (kakaoId) VALUES (?);";

    $st = $pdo->prepare($query);
    $st->execute([$kakaoId]);

    $st = null;
    $pdo = null;

}

function deleteUser($kakaoId)
{
    $pdo = pdoSqlConnect();

    $query = "DELETE FROM USER_TB 
               WHERE kakaoId = ?";

    $st = $pdo->prepare($query);
    $st->execute([$kakaoId]);

    $st = null;
    $pdo = null;

}

