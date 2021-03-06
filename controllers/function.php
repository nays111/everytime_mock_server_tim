<?php

use Firebase\JWT\JWT;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

function getSQLErrorException($errorLogs, $e, $req)
{
    $res = (Object)Array();
    http_response_code(500);
    $res->code = 500;
    $res->message = "SQL Exception -> " . $e->getTraceAsString();
    echo json_encode($res);
    addErrorLogs($errorLogs, $res, $req);
}

function isValidHeader($jwt, $key)
{
    try {
        $data = getDataByJWToken($jwt, $key);
        //로그인 함수 직접 구현 요함
        return isValidUser($data->id, $data->pw);
    } catch (\Exception $e) {
        return false;
    }
}

//패스워드 형식 검사
function isValidPasswordForm($password)
{
    return preg_match("/^.{3,20}$/", $password); //4~20자리
}

//아이디 형식 검사
function isValidIDForm($userID){
    return preg_match("/^[a-zA-Z]\w{3,20}$/", $userID);
}
//폰 형식 검사
function isValidPhoneNumberForm($phone)
{
    return preg_match("/^01[0-9]{8,9}$/", $phone); //01~~~~로 시작해야댐
}

//이메일 형식 검사
function isValidEmailForm($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

//닉네임 형식 검사
function isValidNickNameForm($userNickname){
    return preg_match("/^[ㄱ-ㅎ|가-힣|a-z|A-Z|0-9|\*]{2,7}+$/",$userNickname); //2~7자리
}

//학번 형식 검사
function isValidYearForm($year){
    return preg_match("/^[20]{2}[0-9]{2,4}/",$year); //4자리
}

//강좌코드 형식 검사
function isValidClassCodeForm($code){
    return preg_match("/^[20]{2}[0-9]{2,4}/",$code); //4자리
}
//이미지 url형식
function isValidImageForm($image){
    return preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$image);
}
//이미지 형식, gif,png,jpg로만 끝나야함
function isValidImageType($image){
    return preg_match("/\.(gif|jpg|png)$/i", $image);
}



//유효한 익명여부값인지 체크하기
function isValidUserStatus($userStatus){
    if($userStatus == 0 or $userStatus == 1)
        return true;
    else return false;
}
function checkNullContentTitle($contentTitle){
    if($contentTitle == null){
        return false;
    }
    else return true;
}

function checkNullContentInf($contentInf){
    if($contentInf== null){
        return false;
    }
    else return true;
}

function isValidSemesterForm($semester){
    if($semester == 0 or $semester == 1){
        return true;
    }else return false;
}

function fcmSend($to)
{   //  Fcm Token 을 받아 알림 푸시
    $apiKey = 'AAAA8SCvsUE:APA91bEQtOUq0UxVp7y4xizZD8-jAx-jQ6qpEiWJfEGZodW2ewL940yKSZwL_OB7ppNXtN-1ZViHlZZUzh_PV6gfXPRaXoW9zg9SDf_N9S5TFOveA8YvGgp3Y1hcmSobR51fIEd3_BcZ';
    $title = "댓글 달림";
    $body = "게시물에 댓글이 달렸습니다";
    $notification = array('title' => $title, 'body' => $body, 'sound' => 'default', 'badge' => '1');
    $arrayToSend = array('to' => $to, 'notification' => $notification, 'priority' => 'high');
    $json = json_encode($arrayToSend);
    $headers = array('Authorization: key=' . $apiKey, 'Content-Type: application/json');
    $url = 'https://fcm.googleapis.com/fcm/send';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);

}
/*
function sendFcm($fcmToken, $data, $key, $deviceType)
{
    $url = 'https://fcm.googleapis.com/fcm/send';

    $headers = array(
        'Authorization: key=' . $key,
        'Content-Type: application/json'
    );

    $fields['data'] = $data;

    if ($deviceType == 'IOS') {
        $notification['title'] = $data['title'];
        $notification['body'] = $data['body'];
        $notification['sound'] = 'default';
        $fields['notification'] = $notification;
    }

    $fields['to'] = $fcmToken;
    $fields['content_available'] = true;
    $fields['priority'] = "high";

    $fields = json_encode($fields, JSON_NUMERIC_CHECK);

//    echo $fields;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

    $result = curl_exec($ch);
    if ($result === FALSE) {
        //die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);
    return $result;
}*/

function getTodayByTimeStamp()
{
    return date("Y-m-d H:i:s");
}

function getJWToken($id, $pw, $secretKey)
{
    $data = array(
        'date' => (string)getTodayByTimeStamp(),
        'id' => (string)$id,
        'pw' => (string)$pw
    );

//    echo json_encode($data);

    return $jwt = JWT::encode($data, $secretKey);

//    echo "encoded jwt: " . $jwt . "n";
//    $decoded = JWT::decode($jwt, $secretKey, array('HS256'))
//    print_r($decoded);
}

function getDataByJWToken($jwt, $secretKey)
{
    try{
        $decoded = JWT::decode($jwt, $secretKey, array('HS256'));
    }catch(\Exception $e){
        return "";
    }

//    print_r($decoded);
    return $decoded;

}


function checkAndroidBillingReceipt($credentialsPath, $token, $pid)
{

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $credentialsPath);
    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->addScope("https://www.googleapis.com/auth/androidpublisher");
    $client->setSubject("USER_ID.iam.gserviceaccount.com");


    $service = new Google_Service_AndroidPublisher($client);
    $optParams = array('token' => $token);

    return $service->purchases_products->get("PACKAGE_NAME", $pid, $token);
}


function addAccessLogs($accessLogs, $body)
{
    if (isset($_SERVER['HTTP_X_ACCESS_TOKEN']))
        $logData["JWT"] = getDataByJWToken($_SERVER['HTTP_X_ACCESS_TOKEN'], JWT_SECRET_KEY);
    $logData["GET"] = $_GET;
    $logData["BODY"] = $body;
    $logData["REQUEST_METHOD"] = $_SERVER["REQUEST_METHOD"];
    $logData["REQUEST_URI"] = $_SERVER["REQUEST_URI"];
//    $logData["SERVER_SOFTWARE"] = $_SERVER["SERVER_SOFTWARE"];
    $logData["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"];
    $logData["HTTP_USER_AGENT"] = $_SERVER["HTTP_USER_AGENT"];
    $accessLogs->addInfo(json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

}

function addErrorLogs($errorLogs, $res, $body)
{
    if (isset($_SERVER['HTTP_X_ACCESS_TOKEN']))
        $req["JWT"] = getDataByJWToken($_SERVER['HTTP_X_ACCESS_TOKEN'], JWT_SECRET_KEY);
    $req["GET"] = $_GET;
    $req["BODY"] = $body;
    $req["REQUEST_METHOD"] = $_SERVER["REQUEST_METHOD"];
    $req["REQUEST_URI"] = $_SERVER["REQUEST_URI"];
//    $req["SERVER_SOFTWARE"] = $_SERVER["SERVER_SOFTWARE"];
    $req["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"];
    $req["HTTP_USER_AGENT"] = $_SERVER["HTTP_USER_AGENT"];

    $logData["REQUEST"] = $req;
    $logData["RESPONSE"] = $res;

    $errorLogs->addError(json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

//        sendDebugEmail("Error : " . $req["REQUEST_METHOD"] . " " . $req["REQUEST_URI"] , "<pre>" . json_encode($logData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "</pre>");
}


function getLogs($path)
{
    $fp = fopen($path, "r", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!$fp) echo "error";

    while (!feof($fp)) {
        $str = fgets($fp, 10000);
        $arr[] = $str;
    }
    for ($i = sizeof($arr) - 1; $i >= 0; $i--) {
        echo $arr[$i] . "<br>";
    }
//        fpassthru($fp);
    fclose($fp);
}
