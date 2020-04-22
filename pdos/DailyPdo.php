<?php

function createPlan($kakaoId, $colorId, $location, $contents, $startTime, $endTime, $scheduleDate, $title)
{

    $pdo = pdoSqlConnect();

    $query = "INSERT INTO DAILY_TB (kakaoId, colorId, location, contents, startTime, endTime, scheduleDate, title)
              VALUES(?, ?, ?, ?, ?, ?, ?, ?)";

    $st = $pdo->prepare($query);
    $st->execute([$kakaoId, $colorId, $location, $contents, $startTime, $endTime, $scheduleDate, $title]);

    $st = null;
    $pdo = null;

}