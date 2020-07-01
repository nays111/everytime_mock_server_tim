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


function getMyNotice($userIdx){
    $pdo=pdoSqlConnect();
    $query = "select notice.noticeIdx as noticeIdx,
       notice.noticeName                                                                   as noticeName,
       content.contentTitle                                                                as contentTitle,
       (case when (timediff(now(), content.createdAt) < \"12:00:00\") then \"new\" else 0 end) as checkNew
from user
         inner join univ using (univName)
         inner join notice using (univIdx)
         inner join myNotice on myNotice.userIdx = user.userIdx and notice.noticeIdx = myNotice.noticeIdx
         inner join content on notice.noticeIdx = content.noticeIdx
         inner join (select content.noticeIdx as ni, max(content.createdAt) as maxtime
                     from content
                     group by content.noticeIdx) as t1
                    on t1.maxtime = content.createdAt and t1.ni = content.noticeIdx
where user.userIdx = ?
order by myNotice.createdAt;
    ";
    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}


/*------------------------------------- 게시물(컨텐츠) 리스트 조회-------------------------------------------------*/
function getContents($noticeIdx){
    $pdo=pdoSqlConnect();
    $query = "
select content.contentIdx                                                                                as contentIdx,  
    (case when content.userStatus = 0 then \"익명\" else user.userNickname end)                          as contentWriter,
       content.contentTitle                                                                             as contentTitle,
       content.contentInf                                                                               as contentInf,
       noticeName                                                                                       as noticeName,
       (case
            when content.contentThumbnailURL is null then \"사진없음\"
            else content.contentThumbnailURL end)                                                       as contentThumbnailImage,
       case
           when timediff(now(), content.createdAt) < \"00:01:00\" then '방금'
           when timediff(now(), content.createdAt) < \"01:00:00\"
               then concat(minute(timediff(now(), content.createdAt)), '분전')
           else date_format(content.createdAt, \"%m/%d %H:%i\") end                                       as writeDay,
       (select count(*) from contentLike where content.contentIdx = contentLike.contentIdx)             as countLike,
       (select count(*) from comment where comment.contentIdx = content.contentIdx)                     as countComment,
       (select count(*) from contentURL where contentURL.contentIdx = content.contentIdx)               as countImage
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
/*------------------------------------- 특정 게시물 (컨텐츠) 조회-------------------------------------------------*/
function getContent($contentIdx){
    $pdo=pdoSqlConnect();
    $query = "
select  content.contentIdx as contentIdx,
        (case when content.userStatus = 0 then \"익명\" else user.userNickname end)              as contentWriter,
       content.contentTitle                                                                 as contentTitle,
       content.contentInf                                                                   as contentInf,
       noticeName                                                                           as noticeName,
       case
           when timediff(now(), content.createdAt) < \"00:01:00\" then '방금'
           when timediff(now(), content.createdAt) < \"01:00:00\"
               then concat(minute(timediff(now(), content.createdAt)), '분전')
           else date_format(content.createdAt, \"%m/%d %H:%i\") end                           as writeDay,
       (select count(*) from contentLike where content.contentIdx = contentLike.contentIdx) as countLike,
       (select count(*) from comment where comment.contentIdx = content.contentIdx)         as countComment,
       (select count(*) from everyTimeDB.scrab where scrab.contentIdx = content.contentIdx) as countScrab
from user
         inner join content using (userIdx)
         inner join notice using (noticeIdx)
where content.contentIdx = ?;
";
    $st = $pdo->prepare($query);
    $st->execute([$contentIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}
//컨텐츠(게시물)에 있는 이미지들 가져오기
function getContentImage($contentIdx){
    $pdo=pdoSqlConnect();
    $query = "
select contentIdx, group_concat(contentURL separator ' ') as contentImage 
from contentURL 
where contentIdx=? 
group by contentIdx;
";
    $st = $pdo->prepare($query);
    $st->execute([$contentIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    $res = explode(' ', $res[0]['contentImage']);
    return $res;
}

/*------------------------------------- 댓글 리스트 조회-------------------------------------------------*/
function getComments($userIdx,$contentIdx){
    $pdo=pdoSqlConnect();
    $query = "
select comment.commentIdx,
       comment.commentInf,
       comment.parentIdx,
       (case
            when comment.userIdx = ? and comment.userStatus = 0 then \"익명(글쓴이)\"
            when comment.userStatus = 0 then concat(\"익명\", (select count(c.commentIdx) + 1
                                                           from comment as c
                                                           where c.userStatus = 0
                                                             and c.userIdx != ?
                                                             and c.commentIdx < comment.commentIdx))
            else user.userNickname end)                                                     as commentWriter,
       (select count(*) from commentLike where comment.contentIdx = commentLike.commentIdx) as commentCountLike,
       case
           when timediff(now(), comment.createdAt) < \"00:01:00\"
               then '방금'
           when timediff(now(), comment.createdAt) < \"01:00:00\"
               then concat(minute(timediff(now(), comment.createdAt)), '분전')
           else date_format(comment.createdAt, \"%m/%d %H:%i\") end                           as commentWriteDay
from comment
         inner join user using (userIdx)
where comment.contentIdx = ?
group by comment.commentIdx

";
    $st = $pdo->prepare($query);
    $st->execute([$userIdx,$userIdx,$contentIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}
//컨텐츠(게시물) 작성
function postContent($noticeIdx,$userIdx,$contentThumbnailURL,$contentTitle,$contentInf,$userStatus){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO content(noticeIdx,userIdx,contentThumbnailURL,contentTitle,contentInf,userStatus) VALUES (?,?,?,?,?,?)";

    $st = $pdo->prepare($query);
    $st->execute([$noticeIdx,$userIdx,$contentThumbnailURL,$contentTitle,$contentInf,$userStatus]);

    $st = null;
    $pdo = null;
}
//댓글 작성
function postComment($parentIdx, $contentIdx,$userIdx, $commentInf, $userStatus){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO comment(parentIdx,contentIdx,userIdx,commentInf,userStatus) VALUES (?,?,?,?,?)";

    $st = $pdo->prepare($query);
    $st->execute([$parentIdx, $contentIdx,$userIdx, $commentInf, $userStatus]);

    $st = null;
    $pdo = null;
}

//즐겨찾기 게시판 추가
function postMyNotice($userIdx,$noticeIdx){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO myNotice(userIdx,noticeIdx) VALUES (?,?)";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx,$noticeIdx]);

    $st = null;
    $pdo = null;
}

function deleteMyNotice($userIdx,$noticeIdx){
    $pdo = pdoSqlConnect();
    $query = "delete from myNotice where userIdx=? and noticeIdx=?";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx,$noticeIdx]);

    $st = null;
    $pdo = null;
}

//컨텐츠 좋아요 추가
function postContentLike($contentIdx,$userIdx){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO contentLike(contentIdx,userIdx) VALUES (?,?)";

    $st = $pdo->prepare($query);
    $st->execute([$contentIdx,$userIdx]);

    $st = null;
    $pdo = null;
}

//댓글 좋아요 추가
function postCommentLike($commentIdx,$userIdx){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO commentLike(commentIdx,userIdx) VALUES (?,?)";

    $st = $pdo->prepare($query);
    $st->execute([$commentIdx,$userIdx]);

    $st = null;
    $pdo = null;
}

//스크랩 추가
function postScrab($userIdx,$contentIdx){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO scrab(userIdx,contentIdx) VALUES (?,?)";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx,$contentIdx]);

    $st = null;
    $pdo = null;
}
function deleteScrab($userIdx,$contentIdx){
    $pdo = pdoSqlConnect();
    $query = "delete from scrab where userIdx=? and contentIdx=?";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx,$contentIdx]);

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
