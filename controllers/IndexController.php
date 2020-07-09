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
         * API No. 1
         * API Name : 회원가입 API
         * 마지막 수정 날짜 : 20.06.30
         */
        case "postUser":
            http_response_code(200);

            if(!isValidIDForm($req->userID)){
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "잘못된 ID 형식입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }else if(!isValidPasswordForm($req->pw)){
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "잘못된 비밀번호 형식입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }else if(!isValidNickNameForm($req->userNickname)){
                $res->isSuccess = FALSE;
                $res->code = 203;
                $res->message = "잘못된 형식의 닉네임 입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }else if(!isValidPhoneNumberForm($req->phoneNum)){
                $res->isSuccess = FALSE;
                $res->code = 204;
                $res->message = "잘못된 형식의 휴대폰 번호 입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }else if(!isValidEmailForm($req->email)){
                $res->isSuccess = FALSE;
                $res->code = 205;
                $res->message = "잘못된 형식의 이메일 입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }else if(isRedundantUserID($req->userID)){
                $res->isSuccess = FALSE;
                $res->code = 206;
                $res->message = "이미 등록된 아이디 입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }else if(isRedundantNickname($req->userNickname)){
                $res->isSuccess = FALSE;
                $res->code = 207;
                $res->message = "이미 등록된 닉네임 입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }else if(isRedundantEmail($req->email)){
                $res->isSuccess = FALSE;
                $res->code = 208;
                $res->message = "이미 등록된 이메일 입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }else if(!isValidUniv($req->univName)){
                $res->isSuccess = FALSE;
                $res->code = 209;
                $res->message = "존재하지 않는 대학교입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }else if(!isValidYearForm($req->univYear)){
                $res->isSuccess = FALSE;
                $res->code = 210;
                $res->message = "잘못된 학번 형식입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }


            postUser($req->userID,$req->pw,$req->userNickname,$req->phoneNum,$req->univName,$req->univYear,$req->email);
            /*$result['유저ID']=$req->userID;
            $result['닉네임']=$req->userNickname;
            $result['휴대폰이름']=$req->phoneNum;
            $result['대학이름']=$req->univName;
            $result['학번']=$req->univYear;
            $result['이메일']=$req->email;*/

            $jwt = getJWToken($req->userID,$req->pw,JWT_SECRET_KEY);


            $res->result["jwt"] = $jwt;

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "회원 생성 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 2
         * API Name : 로그인 API
         * 마지막 수정 날짜 : 20.07.02
        */
        case "login":
            http_response_code(200);

            if(!isValidUser($req->userID,$req->pw)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "로그인 실패";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            $jwt = getJWToken($req->userID,$req->pw, JWT_SECRET_KEY);

            $res->inf = login($req->userID,$req->pw); //pw필요없음
            $res->result["jwt"] = $jwt;
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "로그인 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 2-1
         * API Name : 토근 검증 API
         * 마지막 수정 날짜 : 20.07.03
        */
        case "validJWT":
            http_response_code(200);

            if (!isset($_SERVER["HTTP_X_ACCESS_TOKEN"])) {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "토큰 입력 바랍니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

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
            $res->message = "유효한 토큰입니다";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            return;

        /*
        * API No. 3
        * API Name : 유저 정보 조회 API
        * 마지막 수정 날짜 : 20.07.03
        */
        case "getUser":

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

                    $res->result = getUserInfo($userID);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "유저 정보 조회 성공";
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
       * API No. 4
       * API Name : 유저 정보 변경 API
       * 마지막 수정 날짜 : 20.07.04
       */
        case "updateUser":

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
                    $userNickname = $req->userNickname;

                    if(!isValidNickNameForm($userNickname)){
                        $res->isSuccess = FALSE;
                        $res->code = 202;
                        $res->message = "변경할 닉네임을 2~7자리로 입력해주세요";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }else if(isRedundantNickname($userNickname)){
                        $res->isSuccess = FALSE;
                        $res->code = 203;
                        $res->message = "이미 등록된 닉네임 입니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    updateUser($userNickname, $userID);

                    $result["userID"] = $userID;
                    $result["userNickname"]=$userNickname;
                    $res->result = $result;
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "유저 정보 변경 성공";
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
        * API No. 5
        * API Name : 회원 탈퇴 API
        * 마지막 수정 날짜 : 20.07.03
        */
        case "deleteUser":

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
                    deleteUser($userID);
                    $res->result = $userID;
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "회원 탈퇴 성공";
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
          * API No. 6
          * API Name : 광고 리스트 조회 API
          * 마지막 수정 날짜 : 20.07.02
         */


        case "getAds":
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


                    $res->result = getAds();
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "광고 목록 조회 성공";
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
