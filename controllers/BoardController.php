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
      * API No. 5 ('GET', '/boards)
      * API Name : 전체 게시판 종류 조회 API
      * 마지막 수정 날짜 : 20.04.25
      */
        case "boards":
            http_response_code(200);

            $res->result = boards();
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "게시판 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
