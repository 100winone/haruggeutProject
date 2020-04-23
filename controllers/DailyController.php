<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        /*
         * API No. 2 ('POST', '/plans)
         * API Name : 일정입력 API
         * 마지막 수정 날짜 : 20.04.22
         */

        case "createPlan":
            http_response_code(200);
            $colorId = $req->colorId;
            $place = $req->place;
            $contents = $req->contents;
            $startTime = $req->startTime;
            $endTime = $req->endTime;
            $scheduleDate = $req->scheduleDate;
            $title = $req->title;

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"]; // jwt

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $kakaoId = $userInfo->kakaoId;

            createPlan($kakaoId, $colorId, $place, $contents, $startTime, $endTime, $scheduleDate, $title);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "일정 생성 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 3 ('GET', '/plans)
         * API Name : 일정조회 API
         * 마지막 수정 날짜 : 20.04.23
         */
        case "plans":
            http_response_code(200);

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"]; // jwt

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $kakaoId = $userInfo->kakaoId;

            $scheduleDate = $_GET['scheduleDate'];

            $res->result = plans($kakaoId, $scheduleDate);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "일정 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
