<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "index":
            echo "API Server";
            break;
        case "ACCESS_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/access.log");
            break;
        case "ERROR_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/errors.log");
            break;
        /*
         * API No. 0
         * API Name : 테스트 API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "test":
            http_response_code(200);
            $res->result = test();
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 0
         * API Name : 테스트 Path Variable API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "testDetail":
            http_response_code(200);
            $res->result = testDetail($vars["testNo"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 0
         * API Name : 테스트 Body & Insert API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "testPost":
            http_response_code(200);
            $res->result = testPost($req->name);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 3
         * API Name : 즐겨 찾기 게시판 조회 API (홈화면)
         * 마지막 수정 날짜 : 19.04.29
         */
        case "getMyNotice":

            http_response_code(200);
            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];

            if ($jwt) {
                // jwt 유효성 검사
                if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                    $res->isSuccess = FALSE;
                    $res->code = 201;
                    $res->message = "유효하지 않은 토큰입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    addErrorLogs($errorLogs, $res, $req);

                } else {
                    $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                    $userID = $userInfo->id;

                    $res->result = getMyNotice($userID);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "즐겨찾기 게시판 조회 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);

                }
            }else{
                $res->code = 200;
                $res->message = "로그인이 필요합니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            break;
        /*
         * API No. 3
         * API Name : 콘텐츠 리스트 조회 API
         * 마지막 수정 날짜 : 20.07.01
         */

        case "getContents":
            http_response_code(200);

            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];

            if($jwt){
                // jwt 유효성 검사
                if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                    $res->isSuccess = FALSE;
                    $res->code = 200;
                    $res->message = "유효하지 않은 토큰입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    addErrorLogs($errorLogs, $res, $req);
                }else{
                    $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                    $userID = $userInfo->id;

                    $noticeIdx = $vars["noticeIdx"];
                    if (!isValidNotice($noticeIdx)) {
                        $res->isSuccess = FALSE;
                        $res->code = 202;
                        $res->message = "해당 게시판은 존재하지 않습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    $res->result = getContents($noticeIdx);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "컨텐츠 목록 조회 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }else{
                $res->code = 201;
                $res->message = "로그인이 필요합니다.";
                return;
            }
            break;
        /*
         * API No.
         * API Name : 콘텐츠 조회 API
         * 마지막 수정 날짜 : 20.07.01
         */
        case "getContent":
            http_response_code(200);

            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];

            if($jwt){
                // jwt 유효성 검사
                if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                    $res->isSuccess = FALSE;
                    $res->code = 200;
                    $res->message = "유효하지 않은 토큰입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    addErrorLogs($errorLogs, $res, $req);
                }else{
                    $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                    $userID = $userInfo->id;

                    $contentIdx = $vars["contentIdx"];
                    if (!isValidContent($contentIdx)) {
                        $res->isSuccess = FALSE;
                        $res->code = 202;
                        $res->message = "해당 컨텐츠는 존재하지 않습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    $res->result = getContent($contentIdx);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "컨텐츠 조회 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }else{
                $res->code = 201;
                $res->message = "로그인이 필요합니다.";
                return;
            }
            break;
        /*
         * API No.
         * API Name : 댓글 조회 API
         * 마지막 수정 날짜 : 20.07.01
         */
        case getComments:
            http_response_code(200);

            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];

            if($jwt){
                // jwt 유효성 검사
                if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                    $res->isSuccess = FALSE;
                    $res->code = 200;
                    $res->message = "유효하지 않은 토큰입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    addErrorLogs($errorLogs, $res, $req);
                }else{
                    $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                    $userID = $userInfo->id;
                    $userIdx = getUserIdx($userID);

                    $contentIdx = $vars["contentIdx"];
                    if (!isValidContent($contentIdx)) {
                        $res->isSuccess = FALSE;
                        $res->code = 202;
                        $res->message = "해당 컨텐츠는 존재하지 않습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    $res->result = getComments($userIdx,$contentIdx);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "컨텐츠 조회 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }else{
                $res->code = 201;
                $res->message = "로그인이 필요합니다.";
                return;
            }
            break;



    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
