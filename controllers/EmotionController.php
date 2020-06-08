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
         * API No. 16 ('PATCH', '/plans/{planNo}/emotion/{emotionId}')
         * API Name : 일정 감정 등록 및 수정 API
         * 마지막 수정 날짜 : 20.05.09
         */
        case "modifyEmotion":
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
            $no = $vars["planNo"];
            $emotionId = $vars["emotionId"];


            if (!isPlan($kakaoId, $no)) {
                $res->isSucces = FALSE;
                $res->code = 202;
                $res->message = "존재하지 않는 일정입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            if($emotionId >= 0 && $emotionId <= 6){
                modifyEmotion($kakaoId, $no, $emotionId);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "감정 수정 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else {
                $res->isSucces = FALSE;
                $res->code = 203;
                $res->message = "0번에서 6번 사이의 숫자만 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

        /*
        * API No. 15 ('POST', '/comment/{postId}')
        * API Name : 감정 댓글 작성 및 수정 API
        * 마지막 수정 날짜 : 20.05.09
        */
        case "createComment":
            http_response_code(200);
            $emotionId = $req->emotionId;
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
            $postId = $vars["postId"];
            $fcmToken = getFcmTokenByPost($postId);
//            echo $fcmToken;
            if (!isPost($postId)) {
                $res->isSucces = FALSE;
                $res->code = 202;
                $res->message = "존재하지 않는 글 입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            if($emotionId < 0 || $emotionId > 6){
                $res->isSucces = FALSE;
                $res->code = 203;
                $res->message = "0번에서 6번 사이의 숫자만 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            $postTitle = getPostTitle($postId);

            if(isCommented($kakaoId, $postId)){ // update
                modifyComment($kakaoId, $postId, $emotionId);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "감정 댓글 작성 성공 (기존 댓글 수정)";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else { // insert
                createComment($kakaoId, $postId, $emotionId);
                sendCommentFcm($fcmToken, $postId, $postTitle);
                $res->isSuccess = TRUE;
                $res->code = 101;
                $res->message = "감정 댓글 작성 성공 (초기 댓글 작성)";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
