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
         * API No. 11 ('GET', '/post/scroll)
         * API Name : 최신글 조회 API
         * 마지막 수정 날짜 : 20.05.10
         * 없으면 최신글
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

            $lastNo = $_GET['lastNo'];
            if(!$lastNo || isLastPost($kakaoId, $lastNo)){
                $res->result = posts($kakaoId ,$lastNo);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "글 조회 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else if(!isLastPost($kakaoId, $lastNo)){
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "더 이상 존재하는 글이 없습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
        /*
             * API No. 10 ('POST', '/post)
             * API Name : 글 작성 API
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

        /*
         * API No. 12 ('PATCH', '/post/{postId})
         * API Name : 본인 글 수정 API
         * 마지막 수정 날짜 : 20.05.06
         */
        case "modifyPost":
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
            $postId = $vars["postId"];
            $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $kakaoId = $userInfo->kakaoId;

            if(!isMyPost($kakaoId, $postId)){
                $res->isSucces = FALSE;
                $res->code = 202;
                $res->message = "존재하지 않는 글 입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            $title = $req->title;
            $postContents = $req->postContents;

            modifyPost($kakaoId, $postId, $title, $postContents);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "글 수정이 완료되었습니다.";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            return;

        /*
        * API No. 13 ('DELETE', '/post/{postId})
        * API Name : 본인 글 삭제 API
        * 마지막 수정 날짜 : 20.05.10
        */
        case "deletePost":
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
            $postId = $vars["postId"];
            $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $kakaoId = $userInfo->kakaoId;

            if(!isMyPost($kakaoId, $postId)){
                $res->isSucces = FALSE;
                $res->code = 202;
                $res->message = "존재하지 않는 글 입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            deletePost($kakaoId, $postId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "본인 글 삭제 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 9 ('GET', '/post)
         * API Name : 커뮤니티 기본화면(최신글 제외) API
         * 마지막 수정 날짜 : 20.05.10
         * sort=mine
         * sort=mycvomment
         * sort=favorite
         */
        case "basicPosts":
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
            $res->result = basicPosts($kakaoId, $sort);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "글 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
        * API No. 14 ('GET', '/post/{postId})
        * API Name : 글 상세 조회 API
        * 마지막 수정 날짜 : 20.05.10
        */
        case "detailPost":
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
            $postId = $vars["postId"];

            if(!isPost($postId)){
                $res->isSucces = FALSE;
                $res->code = 202;
                $res->message = "존재하지 않는 글 입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            $res->result = detailPost($kakaoId, $postId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "글 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
        * API No. 16 ('POST', '/post/{postId}/favorite)
        * API Name : 글 즐겨찾기 추가 및 수정 API
        * 마지막 수정 날짜 : 20.05.11
        */

        case "favoritePost":
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
            $postId = $vars["postId"];

            if(!isPost($postId)){
                $res->isSucces = FALSE;
                $res->code = 202;
                $res->message = "존재하지 않는 글 입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            if(isAlreadyFavorite($kakaoId, $postId)){
                if(favoriteState($kakaoId, $postId)){ // 즐겨찾기 해제로 변경함 기존 상태에
                    updateFavoriteState($kakaoId, $postId);
                    $res->isSuccess = TRUE;
                    $res->code = 101;
                    $res->message = "글 즐겨찾기 상태에 추가되었습니다.(기존 상태 변경)";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                } else { // 기존상태 즐겨찾기 해제
                    updateFavoriteState($kakaoId, $postId);
                    $res->isSuccess = FALSE;
                    $res->code = 203;
                    $res->message = "글 즐겨찾기 상태가 해제되었습니다.(기존 상태 변경)";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
            }
            favoritePost($kakaoId, $postId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "글 즐겨찾기 상태에 추가되었습니다.(처음 추가됨)";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}