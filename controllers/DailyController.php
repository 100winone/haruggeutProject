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
            $isPriority = $req->isPriority;

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

            createPlan($kakaoId, $colorId, $place, $contents, $startTime, $endTime, $scheduleDate, $title, $isPriority);
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

            $date = $_GET['date'];

            $res->result = plans($kakaoId, $date);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "일정 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
        * API No. 4 ('DELETE', '/plans/{planNo})
        * API Name : 일정삭제 API
        * 마지막 수정 날짜 : 20.04.26
        */
        case "deletePlan":
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

            if (!isPlan($kakaoId, $no)) {
                $res->isSucces = FALSE;
                $res->code = 202;
                $res->message = "존재하지 않는 일정입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            deletePlan($kakaoId, $no);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "일정 삭제 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
                 * API No. 5 ('GET', '/plans/{planNo})
                 * API Name : 일정 상세 조회 API
                 * 마지막 수정 날짜 : 20.05.06
                 */
        case "detailPlan":
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

            $res->result = detailPlan($no, $kakaoId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "일정 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
        * API No. 6 ('PATCH', '/plans/{planNo}/favorite)
        * API Name : 즐겨찾기 추가 제거 API
        * 마지막 수정 날짜 : 20.04.30
        */
        case "favoritePlan":
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
            $no = $vars["planNo"];
            $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $kakaoId = $userInfo->kakaoId;

            if(!isPlan($kakaoId, $no)){
                $res->isSucces = FALSE;
                $res->code = 202;
                $res->message = "존재하지 않는 일정입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if(isFavorite($kakaoId, $no)){
                updateFavoriteStatus($kakaoId, $no);
                $res->isSuccess = TRUE;
                $res->code = 101;
                $res->message = "즐겨찾기 선택 추가되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else{
                updateFavoriteStatus($kakaoId, $no);
                $res->isSuccess = TRUE;
                $res->code = 102;
                $res->message = "즐겨찾기 선택 해제되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

        /*
                * API No. 10 ('PATCH', '/plans/{planNo})
                * API Name : 일정 수정  API
                * 마지막 수정 날짜 : 20.05.06
                */
        case "editPlan":
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
            $no = $vars["planNo"];
            $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $kakaoId = $userInfo->kakaoId;

            if(!isPlan($kakaoId, $no)){
                $res->isSucces = FALSE;
                $res->code = 202;
                $res->message = "존재하지 않는 일정입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            $colorId = $req->colorId;
            $place = $req->place;
            $contents = $req->contents;
            $startTime = $req->startTime;
            $endTime = $req->endTime;
            $scheduleDate = $req->scheduleDate;
            $title = $req->title;
            $isPriority = $req->isPriority;

            editPlan($kakaoId, $no, $colorId, $place, $contents, $startTime, $endTime, $scheduleDate, $title, $isPriority);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "일정 수정이 완료되었습니다.";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            return;

    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
