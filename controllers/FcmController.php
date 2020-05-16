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
       * API No. 16 ('POST', '/post/{postId}/favorite)
       * API Name : 글 즐겨찾기 추가 및 수정 API
       * 마지막 수정 날짜 : 20.05.11
       */

        case "updateFcm":
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
            $fcmToken = $req->fcmToken;

            updateFcm($kakaoId, $fcmToken);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "새로운 토큰 정보가 입력되었습니다.";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            return;

    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
