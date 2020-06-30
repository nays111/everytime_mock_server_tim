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

function postUser($userID,$pw,$userNickname,$phoneNum,$univName,$univYear,$email){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO user(userID,pw,userNickname,phoneNum,univName,univYear,email) VALUES ( ? , ? , ? , ? , ? , ? , ? );";
    $st = $pdo->prepare($query);
    $st->execute([$userID,$pw,$userNickname,$phoneNum,$univName,$univYear,$email]);

    $st = null;
    $pdo = null;
}

function login($userID,$pw){
    $pdo = pdoSqlConnect();
    $query = "SELECT userID as 유저ID, userNickname as 닉네임, univName as 대학교 FROM user WHERE userID=? and pw=?";

    $st = $pdo->prepare($query);
    $st->execute([$userID,$pw]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}


function getMyNotice($userID){
    $pdo=pdoSqlConnect();
    $query = "
    select notice.noticeName                                                                   as 게시판이름,
       content.contentTitle                                                                as 글제목,
       (case when (timediff(now(), content.createdAt) < \"12:00:00\") then \"new\" else 0 end) as 최신여부
from user
         inner join univ using (univName)
         inner join notice using (univIdx)
         inner join myNotice on myNotice.userIdx = user.userIdx and notice.noticeIdx = myNotice.noticeIdx
         inner join content on notice.noticeIdx = content.noticeIdx
         inner join (select content.noticeIdx as ni, max(content.createdAt) as maxtime
                     from content
                     group by content.noticeIdx) as t1
                    on t1.maxtime = content.createdAt and t1.ni = content.noticeIdx
where user.userID = ?
order by myNotice.createdAt;
    ";
    $st = $pdo->prepare($query);
    $st->execute([$userID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}



function getContents($noticeIdx){
    $pdo=pdoSqlConnect();
    $query = "
    select (case when content.userStatus = 0 then \"익명\" else user.userNickname end)              as 작성자,
       content.contentTitle                                                                 as 글제목,
       content.contentInf                                                                   as 글내용,
       noticeName                                                                           as 게시판이름,
       case
           when timediff(now(), content.createdAt) < \"01:00:00\"
               then concat(minute(timediff(now(), content.createdAt)), '분전')
           else date_format(content.createdAt, \"%m/%d %H:%i\") end                           as 작성날짜,
       (select count(*) from contentLike where content.contentIdx = contentLike.contentIdx) as 좋아요개수,
       (select count(*) from comment where comment.contentIdx = content.contentIdx)         as 댓글개수
from user
         inner join content using (userIdx)
         inner join notice using (noticeIdx)
where notice.noticeIdx = ?
order by content.createdAt desc;
";
    $st = $pdo->prepare($query);
    $st->execute([$noticeIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
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
