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
        /* ****************************************************************************************************************** */
        /*
         * API No. 7
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
        /* ****************************************************************************************************************** */
        /*
        * API No. 8
        * API Name : 핫 게시물 조회 API (홈화면)
        * 마지막 수정 날짜 : 20.07.03
        */
        case "getHotContentHome":

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

                    $res->result = getHotContentHome($userIdx);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "핫 게시물 조회 성공";
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

        /* ****************************************************************************************************************** */
        /*
        * API No. 9
        * API Name : 실시간 인기글 조회 API (홈화면)
        * 마지막 수정 날짜 : 20.07.03
        */
        case "getPopularContentHome":

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

                    $res->result = getPopularContentHome();
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "실시간 인기글 조회 성공";
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

        /* ****************************************************************************************************************** */
        /*
        * API No. 10
        * API Name : 게시판 리스트 조회 API
        * 마지막 수정 날짜 : 20.07.02
        */
        case "getNoticeList":

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

                    $univName = getUnivName($userID);
                    $univIdx = getUnivIdx($univName);

                    $res->result = getNotice($univIdx);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "게시판 조회 성공";
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
        /* ****************************************************************************************************************** */
        /*
         * API No. 11
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
                        $result['checkStatus']=0;
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
                    $result['checkStatus']=1;
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
        /* ****************************************************************************************************************** */
        /*
         * API No. 12
         * API Name : 특정 게시판에서의 콘텐츠 리스트 조회 API
         * 마지막 수정 날짜 : 20.07.03
         */

        case "getContentsByNotice":
            http_response_code(200);

            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];

            //글 제목과 내용 둘 다로 검색할 수 있기 때문에 하나만 사용
            $keyword=$_GET['keyword']; //쿼리스트링 사용하기위하여 추가


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
                    $noticeIdx = $vars["noticeIdx"];  //path Variable 사용하기 위해 추가
                    if (!isValidNotice($noticeIdx)) {
                        $res->isSuccess = FALSE;
                        $res->code = 202;
                        $res->message = "해당 게시판은 존재하지 않습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    if($keyword){
                        if(!isValidContentTitleAndInf($keyword,$keyword)){
                            $res->isSuccess = FALSE;
                            $res->code = 203;
                            $res->message = "검색 결과가 없습니다";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }elseif(mb_strlen($keyword,'utf-8')==0){
                            $res->isSuccess = FALSE;
                            $res->code = 204;
                            $res->message = "쿼리스트링이 null입니다";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }elseif(mb_strlen($keyword,'utf-8')<2){
                            $res->isSuccess = FALSE;
                            $res->code = 207;
                            $res->message = "두 글자 이상 입력해주세요";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }
                        $res->result = getContentsBySearch($noticeIdx,$keyword,$keyword);
                        $res->isSuccess = TRUE;
                        $res->code = 101;
                        $res->message = "글제목, 내용 검색을 통해 컨텐츠 목록 조회 성공";
                        echo json_encode($res, JSON_NUMERIC_CHECK);

                    }else{
                        $res->result = getContents($noticeIdx);
                        $res->isSuccess = TRUE;
                        $res->code = 100;
                        $res->message = "컨텐츠 목록 조회 성공";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                    }
                }
            }else{
                $res->code = 200;
                $res->message = "로그인이 필요합니다.";
                return;
            }
            break;
        /* ****************************************************************************************************************** */
        /*
        * API No. 13
        * API Name : 전체를 대상으로 콘텐츠 리스트 조회 API
        * 마지막 수정 날짜 : 20.07.03
        */

        case "getContents":
            http_response_code(200);

            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];

            //글 제목과 내용 둘 다로 검색할 수 있기 때문에 하나만 사용
            $keyword=$_GET['keyword']; //쿼리스트링 사용하기위하여 추가
            $choice=$_GET['choice'];

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

                    if($keyword){
                        if(!isValidContentTitleAndInf($keyword,$keyword)){
                            $res->isSuccess = FALSE;
                            $res->code = 203;
                            $res->message = "검색 결과가 없습니다";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }elseif(mb_strlen($keyword,'utf-8')==0){
                            $res->isSuccess = FALSE;
                            $res->code = 204;
                            $res->message = "쿼리스트링이 null입니다";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }elseif(mb_strlen($keyword,'utf-8')<2){
                            $res->isSuccess = FALSE;
                            $res->code = 207;
                            $res->message = "두 글자 이상 입력해주세요";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }
                        $res->result = getContentsAllBySearch($keyword,$keyword);
                        $res->isSuccess = TRUE;
                        $res->code = 101;
                        $res->message = "전체 게시판에서 글제목, 내용 검색을 통해 컨텐츠 목록 조회 성공";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;

                    }elseif($choice){
                        if($choice=="hot-content"){
                            $res->result = getHotContent();
                            $res->isSuccess = TRUE;
                            $res->code = 113;
                            $res->message = "핫 게시물 조회 성공";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }elseif($choice=="my-content"){
                            $res->result = getMyContent($userIdx);
                            $res->isSuccess = TRUE;
                            $res->code = 110;
                            $res->message = "내가 쓴 글 조회 성공";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }elseif($choice == "my-comment"){
                            $res->result = getMyComment($userIdx);
                            $res->isSuccess = TRUE;
                            $res->code = 111;
                            $res->message = "댓글 단 글 조회 성공";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }elseif($choice == "scrab"){
                            $res->result = getScrab($userIdx);
                            $res->isSuccess = TRUE;
                            $res->code = 112;
                            $res->message = "스크랩한 글 조회 성공";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }elseif(strlen($choice)==0){

                            $res->isSuccess = FALSE;
                            $res->code = 204;
                            $res->message = "쿼리스트링이 null입니다";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }else{
                            $res->isSuccess = FALSE;
                            $res->code = 205;
                            $res->message = "올바른 값을 입력하주세요";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }
                    }else{
                        $res->isSuccess = FALSE;
                        $res->code = 206;
                        $res->message = "쿼리스트링을 꼭 붙혀주세요";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                }
            }else{
                $res->code = 200;
                $res->message = "로그인이 필요합니다.";
                return;
            }
            break;
        /* ****************************************************************************************************************** */
        /*
         * API No. 14
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
                    $result['contentImageURLList'] = $contentURL=getContentImage($contentIdx);
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
        /* ****************************************************************************************************************** */
        /*
         * API No. 15
         * API Name : 컨텐츠(게시물) 작성 API
         * 마지막 수정 날짜 : 20.07.03
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
                        $res->code = 205;
                        $res->message = "익명 여부 체크가 잘못되었습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }elseif ($req->contentTitle==null){
                        $res->isSuccess = FALSE;
                        $res->code = 203;
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

                    postContent($noticeIdx,$userIdx,$req->contentTitle,$req->contentInf,$req->userStatus);
                    $noticeName = getNoticeName($noticeIdx);

                    $result["noticeIdx"] =$noticeIdx;
                    $result["noticeName"] =$noticeName;
                    $result["contentTitle"]=$req->contentTitle;
                    $result["contentInf"] = $req->contentInf;
                    $result["userStatus"] = $req->userStatus;

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

        /* ****************************************************************************************************************** */
        /*
         * API No. 16
         * API Name : 컨텐츠(게시물) 수정 API
         * 마지막 수정 날짜 : 20.07.04
         */
        case updateContent:
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

                    if(!isValidUserStatus($req->userStatus)) {
                        $res->isSuccess = FALSE;
                        $res->code = 205;
                        $res->message = "익명 여부 체크가 잘못되었습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }elseif ($req->contentTitle==null){
                        $res->isSuccess = FALSE;
                        $res->code = 203;
                        $res->message = "제목을 입력해주세요";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }elseif($req->contentInf==null){
                        $res->isSuccess = FALSE;
                        $res->code = 204;
                        $res->message = "내용을 입력하세요";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }elseif (!isValidContent($contentIdx)) {
                        $res->isSuccess = FALSE;
                        $res->code = 202;
                        $res->message = "해당 게시물은 존재하지 않습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }elseif(isMyContent($contentIdx)!=$userIdx){
                        $res->isSuccess = FALSE;
                        $res->code = 206;
                        $res->message = "본인이 쓰지 않은 댓글은 수정할 수 없습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    updateContent($req->contentTitle,$req->contentInf,$req->userStatus,$contentIdx);

                    $result["contentIdx"]=$contentIdx;
                    $res->result = $result;

                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "컨텐츠(게시물) 수정 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }else{
                $res->code = 200;
                $res->message = "로그인이 필요합니다.";
                return;
            }
            break;

        /* ****************************************************************************************************************** */
        /*
         * API No. 17
         * API Name : 컨텐츠(게시물) 삭제 API
         * 마지막 수정 날짜 : 20.07.04
         */
        case deleteContent:
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
                        $res->message = "해당 게시물은 존재하지 않습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }elseif(isMyContent($contentIdx)!=$userIdx){
                        $res->isSuccess = FALSE;
                        $res->code = 203;
                        $res->message = "본인이 쓰지 않은 댓글은 삭제할 수 없습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    deleteContent($contentIdx);

                    $result["contentIdx"]=$contentIdx;
                    $res->result = $result;

                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "컨텐츠(게시물) 삭제 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }else{
                $res->code = 200;
                $res->message = "로그인이 필요합니다.";
                return;
            }
            break;
        /* ****************************************************************************************************************** */
        /*
         * API No. 18
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
                        $result['checkStatus']=0;
                        $res->result = $result;
                        $res->isSuccess= TRUE;
                        $res->code=101;
                        $res->message = "스크랩을 취소했습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }elseif(isMyContent($req->contentIdx)==$userIdx){
                        $res->isSuccess = FALSE;
                        $res->code = 203;
                        $res->message = "내가 쓴 글은 스크랩할 수 없습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);

                        return;
                    }

                    postScrab($userIdx,$req->contentIdx); // 스크랩 추가
                    $result['contentIdx']=$req->contentIdx;
                    $result['contentTitle']=$contentTitle;
                    $result['checkStatus']=1;
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
        /* ****************************************************************************************************************** */
        /*
         * API No. 19
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
                    }else if(isMyContent($req->contentIdx)==$userIdx){
                        $res->isSuccess= FALSE;
                        $res->code=204;
                        $res->message = "내가 쓴 글은 공감할 수 없습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    postContentLike($req->contentIdx,$userIdx); // 게시물 공감 추가
                    $result['contentIdx']=$req->contentIdx;
                    $result['contentTitle']=$contentTitle;
                    $result['checkStatus']=1;
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
        /* ****************************************************************************************************************** */
        /*
         * API No. 20
         * API Name : 댓글 조회 API
         * 마지막 수정 날짜 : 20.07.01
         */
        case "getComments":
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
        /* ****************************************************************************************************************** */
        /*
         * API No. 21
         * API Name : 댓글 작성 API
         * 마지막 수정 날짜 : 20.07.04
         */
        case postComment:
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

                    if(!isValidUserStatus($req->userStatus)) {
                        $res->isSuccess = FALSE;
                        $res->code = 205;
                        $res->message = "익명 여부 체크가 잘못되었습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    elseif($req->commentInf==null){
                        $res->isSuccess = FALSE;
                        $res->code = 204;
                        $res->message = "댓글 내용을 입력하세요";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }elseif (!isValidContent($contentIdx)) {
                        $res->isSuccess = FALSE;
                        $res->code = 202;
                        $res->message = "해당 컨텐츠(게시물)은 존재하지 않습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    postComment($contentIdx,$userIdx,$req->commentInf,$req->userStatus);

                    $result["contentIdx"]=$contentIdx;
                    $result["commentInf"] = $req->commentInf;
                    $result["userStatus"] = $req->userStatus;

                    $res->result = $result;

                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "댓글 작성 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }else{
                $res->code = 200;
                $res->message = "로그인이 필요합니다.";
                return;
            }
            break;


        /* ****************************************************************************************************************** */

        /*
         * API No. 22
         * API Name : 댓글 삭제 API
         * 마지막 수정 날짜 : 20.07.04
         */
        case "deleteComment":
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
                    $commentIdx = $vars["commentIdx"];

                    if (!isValidComment($commentIdx)) {
                        $res->isSuccess = FALSE;
                        $res->code = 202;
                        $res->message = "해당 댓글은 존재하지 않습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }elseif(isMyComment($commentIdx)!=$userIdx){
                        $res->isSuccess = FALSE;
                        $res->code = 203;
                        $res->message = "본인이 쓰지 않은 댓글은 지울 수 없습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    deleteComment($commentIdx);

                    $result["commentIdx"]=$commentIdx;
                    $res->result = $result;

                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "댓글 삭제 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }else{
                $res->code = 200;
                $res->message = "로그인이 필요합니다.";
                return;
            }
            break;
        /* ****************************************************************************************************************** */
        /*
        * API No. 24
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
                    }elseif(isMyComment($req->commentIdx)==$userIdx){
                        $res->isSuccess= FALSE;
                        $res->code=204;
                        $res->message = "내가 쓴 댓글은 공감할 수 없습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    postCommentLike($req->commentIdx,$userIdx); // 댓글 공감 추가
                    $result['commentIdx']=$req->commentIdx;
                    $result['commentInf']=$commentInf;
                    $result['checkStatus']=1;
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

        /* ****************************************************************************************************************** */
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
