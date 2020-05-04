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
         * API No. 7 ('GET', '/posts)
         * API Name : 포스팅 조회 API
         * 마지막 수정 날짜 : 20.05.05
         * 없으면 최신글
         * sort=recent
         * sort=popular
         * sort=mine
         */
        case "posts":
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

            $sort = $_GET['sort'];

            $res->result = posts($kakaoId, $sort);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "일정 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
             * API No. 8 ('POST', '/plans)
             * API Name : 일정입력 API
             * 마지막 수정 날짜 : 20.05.05
             */

        case "createPost":
            http_response_code(200);
            $title = $req->title;
            $postContents = $req->postContents;

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

            createPost($kakaoId, $title, $postContents);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "포스팅 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
