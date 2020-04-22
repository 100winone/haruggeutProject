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
         * API No. 0
         * API Name : JWT 유효성 검사 테스트 API
         * 마지막 수정 날짜 : 19.04.25
         */
        case "validateJwt":
            // jwt 유효성 검사

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 1
         * API Name : JWT 생성 API (로그인)
         * 마지막 수정 날짜 : 20.04.21
         */
        case "createJwt":
            // jwt 유효성 검사
            http_response_code(200);
            $kakaoId = $req->kakaoId;
            if(!isValidUser($kakaoId)){
                createUser($kakaoId);
                $jwt = getJWToken($kakaoId, JWT_SECRET_KEY);
                $res->jwt = $jwt;
                $res->isSuccess = TRUE;
                $res->code = 101;
                $res->message = "토큰 발급 완료(첫 로그인)";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            $jwt = getJWToken($kakaoId, JWT_SECRET_KEY);
            $res->jwt = $jwt;
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "토큰 발급 완료(기존 회원)";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
