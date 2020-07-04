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
         * API No. 25
         * API Name : 전체 강좌 조회(검색 포함) API
         * 마지막 수정 날짜 : 20.07.05
         */
        case "getClasses":

            http_response_code(200);
            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];

            $name=$_GET['name']; //과목명을 통한 검색용
            $professor=$_GET['professor']; //교수명을 통한 검색용
            $code=$_GET['code']; //과목코드를 통한 검색용
            $room=$_GET['room']; //장소를 통한 검색용

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
//과목명으로 검색하는 경우
                    if($name){
                        if(!isValidClassName($name)){
                            $res->isSuccess = FALSE;
                            $res->code = 205;
                            $res->message = "검색결과가 없습니다";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }elseif(strlen($name)==0){
                            $res->isSuccess = FALSE;
                            $res->code = 204;
                            $res->message = "쿼리스트링이 null입니다";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }elseif(strlen($name)<2){
                            $res->isSuccess = FALSE;
                            $res->code = 206;
                            $res->message = "2글자 이상 입력해주세요";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }else{
                            $result = getClassesByName($name);
                            $res->result = $result;
                            $res->isSuccess = TRUE;
                            $res->code = 101;
                            $res->message = "과목명 검색을 통한 강좌 리스트 조회 성공";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                        }

//교수명으로 검색하는 경우
                    }elseif($professor){
                        if(!isValidClassProfessor($professor)){
                            $res->isSuccess = FALSE;
                            $res->code = 205;
                            $res->message = "검색결과가 없습니다";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;

                        }elseif(strlen($professor)<2){
                            $res->isSuccess = FALSE;
                            $res->code = 206;
                            $res->message = "2글자 이상 입력해주세요";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }
                        elseif(strlen($professor)==0){
                            $res->isSuccess = FALSE;
                            $res->code = 204;
                            $res->message = "쿼리스트링이 null입니다";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }
                        $result = getClassesByProfessor($professor);
                        $res->result = $result;
                        $res->isSuccess = TRUE;
                        $res->code = 101;
                        $res->message = "교수명 검색을 통한 강좌 리스트 조회 성공";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
//과목코드로 검색하는 경우
                    }elseif($code){
                        if(!isValidClassCode($code)){
                            $res->isSuccess = FALSE;
                            $res->code = 205;
                            $res->message = "검색결과가 없습니다";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }
                        elseif(strlen($code)<2){
                            $res->isSuccess = FALSE;
                            $res->code = 206;
                            $res->message = "2글자 이상 입력해주세요";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }else if(strlen($code)==0){
                            $res->isSuccess = FALSE;
                            $res->code = 204;
                            $res->message = "null";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }
                        $result = getClassesByCode($code);
                        $res->result = $result;
                        $res->isSuccess = TRUE;
                        $res->code = 102;
                        $res->message = "과목코드 검색을 통한 강좌 리스트 조회 성공";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
//강의실로 검색하는 경우
                    }elseif($room){
                        if(!isValidClassRoom($room)){
                            $res->isSuccess = FALSE;
                            $res->code = 205;
                            $res->message = "검색결과가 없습니다";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }
                        elseif(strlen($room)<2){
                            $res->isSuccess = FALSE;
                            $res->code = 204;
                            $res->message = "2글자 이상 입력해주세요";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }
                        elseif(strlen($room)==0){
                            $res->isSuccess = FALSE;
                            $res->code = 204;
                            $res->message = "쿼리스트링이 null입니다";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }
                        $result = getClassesByRoom($room);
                        $res->result = $result;
                        $res->isSuccess = TRUE;
                        $res->code = 103;
                        $res->message = "강의실 검색을 통한 강좌 리스트 조회 성공";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                    }else{
                        $result = getClasses();
                        $res->result = $result;
                        $res->isSuccess = TRUE;
                        $res->code = 100;
                        $res->message = "전체 강좌 리스트 조회 성공";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                    }
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
        * API No. 25
        * API Name : 특정 강좌 조회API
        * 마지막 수정 날짜 : 20.07.05
        */
        case "getClass":

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

                    $classIdx = $vars["classIdx"];
                    if(!isValidClass($classIdx)){
                        $res->isSuccess = FALSE;
                        $res->code = 203;
                        $res->message = "존재하지 않는 강좌입니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                    }else{
                        $result = getClass($classIdx);
                        //array안에 array
                        /*foreach ($result as $key => $value){
                            settype($result[$key]['classIdx'], "integer");

                            foreach ($result[$key]['Time'] as $TimeKey => $imgValue){
                                settype($result[$key]['Time'][$TimeKey]['classIdx'], "integer");
                            }
                        }*/
                        $res->result = $result;
                        $res->isSuccess = TRUE;
                        $res->code = 100;
                        $res->message = "특정 강좌 조회 성공";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                    }

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
        /* ****************************************************************************************************************** */
        /* ****************************************************************************************************************** */
        /* ****************************************************************************************************************** */
        /* ****************************************************************************************************************** */
        /* ****************************************************************************************************************** */
        /* ****************************************************************************************************************** */
        /* ****************************************************************************************************************** */
        /* ****************************************************************************************************************** */
        /* ****************************************************************************************************************** */
        /* ****************************************************************************************************************** */
        /* ****************************************************************************************************************** */
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
