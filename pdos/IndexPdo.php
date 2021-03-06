<?php

//READ
function test()
{
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM user;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

//READ
function testDetail($testNo)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM Test WHERE no = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$testNo]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}


function testPost($name)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO Test (name) VALUES (?);";

    $st = $pdo->prepare($query);
    $st->execute([$name]);

    $st = null;
    $pdo = null;

}


function isValidUser($id, $pw){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM user WHERE userID= ? AND pw = ?) AS exist;";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $pw]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);

}

//회원가입
function postUser($userID,$pw,$userNickname,$phoneNum,$univName,$univYear,$email){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO user(userID,pw,userNickname,phoneNum,univName,univYear,email) VALUES ( ? , ? , ? , ? , ? , ? , ? );";
    $st = $pdo->prepare($query);
    $st->execute([$userID,$pw,$userNickname,$phoneNum,$univName,$univYear,$email]);

    $st = null;
    $pdo = null;
}

//로그인 함수
function login($userID,$pw){
    $pdo = pdoSqlConnect();
    $query = "SELECT userID as 유저ID, userNickname as 닉네임, univName as 대학교 FROM user WHERE userID=? and pw=?";

    $st = $pdo->prepare($query);
    $st->execute([$userID,$pw]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res; //[0] 추가해서
}

/*
//유저 아이디로 유저 인덱스 가져오깅
function getUserIdx($userID){
    $pdo = pdoSqlConnect();
    $st = $pdo->prepare('select userIdx from user where userID = :userID');
    $st->bindParam(':userID', $userID);
    $st->execute();
    $res = $st->fetch();
    $st = null;
    $pdo = null;
    return intval($res["userIdx"]);
}*/

function getUserIdx($userID){
    $pdo = pdosqlConnect();
    $query = "select userIdx from user where userID=?";
    $st = $pdo -> prepare($query);
    $st->execute([$userID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return $res[0]['userIdx'];

}

//광고 조회
function getAds(){
    $pdo = pdoSqlConnect();
    $query = "SELECT adIdx,adThumbnaillURL FROM ad;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getUserInfo($userID){
    $pdo = pdoSqlConnect();
    $query = "
select user.userNickname, user.userID, user.univName, concat(user.univYear, \"학번\") as univYear
from user
where userID = ?   
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getUserNickname($userID){
    $pdo = pdoSqlConnect();
    $query = "
select user.userNickname
from user
where userID = ?   
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}



//유효한 대학 이름 인지 검사
function isValidUniv($univName){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM univ WHERE univName= ?) AS validUnivName;";
    $st = $pdo -> prepare($query);
    $st->execute([$univName]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validUnivName"]);
}

//ID중복되는지 검사
function isRedundantUserID($userID){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM user WHERE userID= ?) AS rendundantUser;";
    $st = $pdo -> prepare($query);
    $st->execute([$userID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["rendundantUser"]);
}

//중복된 닉네임인지 검사
function isRedundantNickname($userNickname){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM user WHERE userNickname= ?) AS redundantUserNickname;";
    $st = $pdo -> prepare($query);
    $st->execute([$userNickname]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["redundantUserNickname"]);
}

//중복된 이메일인지 검사
function isRedundantEmail($email){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM user WHERE email= ?) AS redundantEmail;";
    $st = $pdo -> prepare($query);
    $st->execute([$email]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["redundantEmail"]);
}

function updateUser($userNickname,$userID){
    $pdo = pdoSqlConnect();
    $query = "update user set userNickname=? where userID=?;";
    $st = $pdo->prepare($query);
    $st->execute([$userNickname,$userID]);

    $st = null;
    $pdo = null;
}

function deleteUser($userID){
    $pdo = pdoSqlConnect();
    $query = "delete from user where userID=?;";
    $st = $pdo->prepare($query);
    $st->execute([$userID]);

    $st = null;
    $pdo = null;
}






// CREATE
//    function addMaintenance($message){
//        $pdo = pdoSqlConnect();
//        $query = "INSERT INTO MAINTENANCE (MESSAGE) VALUES (?);";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message]);
//
//        $st = null;
//        $pdo = null;
//
//    }


// UPDATE
//    function updateMaintenanceStatus($message, $status, $no){
//        $pdo = pdoSqlConnect();
//        $query = "UPDATE MAINTENANCE
//                        SET MESSAGE = ?,
//                            STATUS  = ?
//                        WHERE NO = ?";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message, $status, $no]);
//        $st = null;
//        $pdo = null;
//    }

// RETURN BOOLEAN
//    function isRedundantEmail($email){
//        $pdo = pdoSqlConnect();
//        $query = "SELECT EXISTS(SELECT * FROM USER_TB WHERE EMAIL= ?) AS exist;";
//
//
//        $st = $pdo->prepare($query);
//        //    $st->execute([$param,$param]);
//        $st->execute([$email]);
//        $st->setFetchMode(PDO::FETCH_ASSOC);
//        $res = $st->fetchAll();
//
//        $st=null;$pdo = null;
//
//        return intval($res[0]["exist"]);
//
//    }