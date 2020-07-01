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
         * 마지막 수정 날짜 : 20.07.01
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
                    $userIdx = getUserIdx($userID);

                    $res->result = getMyNotice($userIdx);
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
                    $res->code = 201;
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
                $res->code = 200;
                $res->message = "로그인이 필요합니다.";
                return;
            }
            break;
        /*
         * API No.
         * API Name : 특정 콘텐츠(게시물) 조회 API
         * 마지막 수정 날짜 : 20.07.01
         */
        case "getContent":
            http_response_code(200);

            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];

            if($jwt){
                // jwt 유효성 검사
                if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                    $res->isSuccess = FALSE;
                    $res->code = 201;
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
                    $result = getContent($contentIdx);
                    $result['contentURLList'] = $contentURL=getContentImage($contentIdx);
                    $res->result = $result;
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "컨텐츠 조회 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }else{
                $res->code = 200;
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
                    $res->code = 201;
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
                    $res->message = "댓글 리스트 조회 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }else{
                $res->code = 200;
                $res->message = "로그인이 필요합니다.";
                return;
            }
            break;
        /*
         * API No.
         * API Name : 컨텐츠(게시물) 작성 API
         * 마지막 수정 날짜 : 20.07.01
         */
        case postContent:
            http_response_code(200);

            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];

            if($jwt){
                // jwt 유효성 검사
                if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                    $res->isSuccess = FALSE;
                    $res->code = 201;
                    $res->message = "유효하지 않은 토큰입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    addErrorLogs($errorLogs, $res, $req);
                }else{
                    $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                    $userID = $userInfo->id;
                    $userIdx = getUserIdx($userID);
                    $noticeIdx = $vars["noticeIdx"];

                    if(!isValidUserStatus($req->userStatus)) {
                        $res->isSuccess = FALSE;
                        $res->code = 204;
                        $res->message = "익명 여부 체크가 잘못되었습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }elseif ($req->contentTitle==null){
                        $res->isSuccess = FALSE;
                        $res->code = 204;
                        $res->message = "제목을 입력해주세요";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }elseif($req->contentInf==null){
                        $res->isSuccess = FALSE;
                        $res->code = 204;
                        $res->message = "내용을 입력하세요";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }elseif (!isValidNotice($noticeIdx)) {
                        $res->isSuccess = FALSE;
                        $res->code = 202;
                        $res->message = "해당 게시판은 존재하지 않습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    postContent($noticeIdx,$userIdx,$req->contentThumbnailURL,$req->contentTitle,$req->contentInf,$req->userStatus);
                    $noticeName = getNoticeName($noticeIdx);

                    $result["noticeIdx"] =$noticeIdx;
                    $result["noticeName"] =$noticeName;
                    $result["contentTitle"]=$req->contentTitle;
                    $result["contentInf"] = $req->contentInf;
                    $result["userStatus"] = $req->userStatus;
                    $result["contentThumbnailURL"] = $req->contentThumbnailURL;

                    $res->result = $result;

                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "컨텐츠(게시물) 작성 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }else{
                $res->code = 200;
                $res->message = "로그인이 필요합니다.";
                return;
            }
            break;

        /*
         * API No.
         * API Name : 즐겨찾기 게시판 추가/취소 API
         * 마지막 수정 날짜 : 20.07.01
         */
        case postMyNotice:
            http_response_code(200);

            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];

            if($jwt){
                // jwt 유효성 검사
                if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                    $res->isSuccess = FALSE;
                    $res->code = 201;
                    $res->message = "유효하지 않은 토큰입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    addErrorLogs($errorLogs, $res, $req);
                }else{
                    $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                    $userID = $userInfo->id;
                    $userIdx = getUserIdx($userID);
                    $noticeName = getNoticeName($req->noticeIdx);


                    if(!isValidNotice($req->noticeIdx)){
                        $res->isSuccess = FALSE;
                        $res->code = 202;
                        $res->message = "해당 게시판이 없습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);

                        return;
                    }else if (isValidMyNotice($userIdx,$req->noticeIdx)){
                        deleteMyNotice($userIdx,$req->noticeIdx); //즐겨찾기 해제
                        $result['noticeIdx']=$req->noticeIdx;
                        $result['noticeName']=$noticeName;
                        $res->result = $result;
                        $res->isSuccess= TRUE;
                        $res->code=101;
                        $res->message = "이미 즐겨찾기를 누른 게시판입니다. 즐겨찾기 해제합니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    postMyNotice($userIdx,$req->noticeIdx); // 즐겨찾기 추가
                    $result['noticeIdx']=$req->noticeIdx;
                    $result['noticeName']=$noticeName;
                    $res->result = $result;

                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "즐겨찾기 게시판 추가 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }else{
                $res->code = 200;
                $res->message = "로그인이 필요합니다.";
                return;
            }
            break;
        /*
         * API No.
         * API Name : 스크랩 추가/취소 API
         * 마지막 수정 날짜 : 20.07.02
         */
        case postScrab:
            http_response_code(200);

            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];

            if($jwt){
                // jwt 유효성 검사
                if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                    $res->isSuccess = FALSE;
                    $res->code = 201;
                    $res->message = "유효하지 않은 토큰입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    addErrorLogs($errorLogs, $res, $req);
                }else{
                    $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                    $userID = $userInfo->id;
                    $userIdx = getUserIdx($userID);
                    $contentTitle = getContentTitle($req->contentIdx);


                    if(!isValidContent($req->contentIdx)){
                        $res->isSuccess = FALSE;
                        $res->code = 202;
                        $res->message = "해당 컨텐츠(게시물)가 없습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);

                        return;
                    }else if (isValidScrab($userIdx,$req->contentIdx)){
                        deleteScrab($userIdx,$req->contentIdx); //스크랩 취소
                        $result['contentIdx']=$req->contentIdx;
                        $result['contentTitle']=$contentTitle;
                        $res->result = $result;
                        $res->isSuccess= TRUE;
                        $res->code=101;
                        $res->message = "스크랩을 취소했습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    postScrab($userIdx,$req->contentIdx); // 스크랩 추가
                    $result['contentIdx']=$req->contentIdx;
                    $result['contentTitle']=$contentTitle;
                    $res->result = $result;

                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "스크랩 추가 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }else{
                $res->code = 200;
                $res->message = "로그인이 필요합니다.";
                return;
            }
            break;

        /*
        * API No.
        * API Name : 댓글 공감 추가 API
        * 마지막 수정 날짜 : 20.07.02
        */
        case postCommentLike:
            http_response_code(200);

            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];

            if($jwt){
                // jwt 유효성 검사
                if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                    $res->isSuccess = FALSE;
                    $res->code = 201;
                    $res->message = "유효하지 않은 토큰입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    addErrorLogs($errorLogs, $res, $req);
                }else{
                    $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                    $userID = $userInfo->id;
                    $userIdx = getUserIdx($userID);
                    $commentInf = getCommentInf($req->commentIdx);


                    if(!isValidComment($req->commentIdx)){
                        $res->isSuccess = FALSE;
                        $res->code = 202;
                        $res->message = "해당 댓글이 없습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);

                        return;
                    }else if (isValidCommentLike($req->commentIdx,$userIdx)){

                        /*$result['commentIdx']=$req->commentIdx;
                        $result['commentInf']=$commentInf;
                        $res->result = $result;*/
                        $res->isSuccess= FALSE;
                        $res->code=203;
                        $res->message = "이미 공감한 댓글입니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    postCommentLike($req->commentIdx,$userIdx); // 댓글 공감 추가
                    $result['commentIdx']=$req->commentIdx;
                    $result['commentInf']=$commentInf;
                    $res->result = $result;

                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "댓글 공감 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }else{
                $res->code = 200;
                $res->message = "로그인이 필요합니다.";
                return;
            }
            break;
        /*
         * API No.
         * API Name : 컨텐츠(게시물) 공감 추가 API
         * 마지막 수정 날짜 : 20.07.02
         */
        case postContentLike:
            http_response_code(200);

            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];

            if($jwt){
                // jwt 유효성 검사
                if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                    $res->isSuccess = FALSE;
                    $res->code = 201;
                    $res->message = "유효하지 않은 토큰입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    addErrorLogs($errorLogs, $res, $req);
                }else{
                    $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                    $userID = $userInfo->id;
                    $userIdx = getUserIdx($userID);
                    $contentTitle = getContentTitle($req->contentIdx);


                    if(!isValidContent($req->contentIdx)){
                        $res->isSuccess = FALSE;
                        $res->code = 202;
                        $res->message = "해당 컨텐츠(게시물)가 없습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);

                        return;
                    }else if (isValidContentLike($req->contentIdx,$userIdx)){
                        $res->isSuccess= FALSE;
                        $res->code=203;
                        $res->message = "이미 공감한 게시물입니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    postContentLike($req->contentIdx,$userIdx); // 게시물 공감 추가
                    $result['contentIdx']=$req->contentIdx;
                    $result['contentTitle']=$contentTitle;
                    $res->result = $result;

                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "컨텐츠(게시물) 공감 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }else{
                $res->code = 200;
                $res->message = "로그인이 필요합니다.";
                return;
            }
            break;



    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
