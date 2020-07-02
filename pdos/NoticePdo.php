<?php



//있는 모든 게시판이름만 조회
function getNotice($univIdx){
    $pdo=pdoSqlConnect();
    $query = "select noticeIdx, noticeName from notice where univIdx=?";
    $st = $pdo->prepare($query);
    $st->execute([$univIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

//즐겨찾기 게시판 조회
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

//게시물(컨텐츠) 리스트 조회
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

//특정 게시물 (컨텐츠) 조회
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

//댓글 리스트 조회
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

//즐겨찾기 삭제
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
//스크랩 삭제
function deleteScrab($userIdx,$contentIdx){
    $pdo = pdoSqlConnect();
    $query = "delete from scrab where userIdx=? and contentIdx=?";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx,$contentIdx]);

    $st = null;
    $pdo = null;
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

    return intval($res[0]["validUser"]);
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

//유효한 notice index값인지 검사
function isValidNotice($noticeIdx){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM notice WHERE noticeIdx= ?) AS validNotice;";
    $st = $pdo -> prepare($query);
    $st->execute([$noticeIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validNotice"]);
}



function isValidMyNotice($userIdx,$noticeIdx){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM myNotice WHERE userIdx= ? and noticeIdx= ?) AS validMyNotice;";
    $st = $pdo -> prepare($query);
    $st->execute([$userIdx,$noticeIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validMyNotice"]);
}

function isValidScrab($userIdx,$contentIdx){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM scrab WHERE userIdx= ? and contentIdx= ?) AS validScrab;";
    $st = $pdo -> prepare($query);
    $st->execute([$userIdx,$contentIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validScrab"]);
}

function isValidCommentLike($commentIdx,$userIdx){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM commentLike WHERE commentIdx= ? and userIdx= ?) AS validCommentLike;";
    $st = $pdo -> prepare($query);
    $st->execute([$commentIdx,$userIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validCommentLike"]);
}

function isValidContentLike($contentIdx,$userIdx){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM contentLike WHERE contentIdx= ? and userIdx= ?) AS validContentLike;";
    $st = $pdo -> prepare($query);
    $st->execute([$contentIdx,$userIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validContentLike"]);
}




//유효한 content index 값인지 검사
function isValidContent($contentIdx){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM content WHERE contentIdx= ?) AS validContent;";
    $st = $pdo -> prepare($query);
    $st->execute([$contentIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validContent"]);
}

//유효한 comment index 값인지 검사
function isValidComment($commentIdx){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM comment WHERE commentIdx= ?) AS validComment;";
    $st = $pdo -> prepare($query);
    $st->execute([$commentIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validComment"]);
}




//게시판 이름 가져오기
function getNoticeName($noticeIdx)
{
    $pdo = pdoSqlConnect();
    $st = $pdo->prepare('select noticeName from notice where noticeIdx = :noticeIdx');
    $st->bindParam(':noticeIdx', $noticeIdx);
    $st->execute();
    $res = $st->fetch();
    $st = null;
    $pdo = null;
    return $res["noticeName"];
}

function getUnivIdx($univName){
    $pdo = pdoSqlConnect();
    $st = $pdo->prepare('select univIdx from univ where univName = :univName');
    $st->bindParam(':univName', $univName);
    $st->execute();
    $res = $st->fetch();
    $st = null;
    $pdo = null;
    return $res["univIdx"];
}

function getUnivName($userID){
    $pdo = pdoSqlConnect();
    $st = $pdo->prepare('select univName from user where userID = :userID');
    $st->bindParam(':userID', $userID);
    $st->execute();
    $res = $st->fetch();
    $st = null;
    $pdo = null;
    return $res["univName"];
}


//컨텐츠(게시물) 제목 가져오기
function getContentTitle($contentIdx)
{
    $pdo = pdoSqlConnect();
    $st = $pdo->prepare('select contentTitle from content where contentIdx = :contentIdx');
    $st->bindParam(':contentIdx', $contentIdx);
    $st->execute();
    $res = $st->fetch();
    $st = null;
    $pdo = null;
    return $res["contentTitle"];
}


//댓글 내용 가져오기
function getCommentInf($commentIdx){
    $pdo = pdoSqlConnect();
    $st = $pdo->prepare('select commentInf from comment where commentIdx = :commentIdx');
    $st->bindParam(':commentIdx', $commentIdx);
    $st->execute();
    $res = $st->fetch();
    $st = null;
    $pdo = null;
    return $res["commentInf"];
}

//아직 적용안함
function getMyContent($userIdx){
    $pdo=pdoSqlConnect();
    $query = "
select 
        content.contentIdx as contentIdx,
        (case when content.userStatus = 0 then \"익명\" else user.userNickname end)              as contentWriter,
       content.contentTitle                                                                 as contentTitle,
       content.contentInf                                                                   as contentInf,
       noticeName                                                                           as noticeName,
       (case
            when content.contentThumbnailURL is null then \"사진없음\"
            else content.contentThumbnailURL end)                                           as contentThumbnailImage,
       case
           when timediff(now(), content.createdAt) < \"00:01:00\" then '방금'
           when timediff(now(), content.createdAt) < \"01:00:00\"
               then concat(minute(timediff(now(), content.createdAt)), '분전')
           else date_format(content.createdAt, \"%m/%d %H:%i\") end                           as writeDay,
       (select count(*) from contentLike where content.contentIdx = contentLike.contentIdx) as countLike,
       (select count(*) from comment where comment.contentIdx = content.contentIdx)         as countComment,
       (select count(*) from contentURL where contentURL.contentIdx = content.contentIdx)   as countImage
from user
         inner join content using (userIdx)
         inner join notice using (noticeIdx)
where content.userIdx = ?
order by content.createdAt desc;
";
    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

//아직 적용안함
function getMyComment($userIdx){
    $pdo=pdoSqlConnect();
    $query = "
select distinct comment.contentIdx as contentIdx,
                (case when content.userStatus = 0 then \"익명\" else user.userNickname end)              as contentWriter,
                content.contentTitle                                                                 as contentTitle,
                content.contentInf                                                                   as contentInf,
                noticeName                                                                           as noticeName,
                (case
                     when content.contentThumbnailURL is null then \"사진없음\"
                     else content.contentThumbnailURL end)                                           as contentThumbnailImage,
                case
                    when timediff(now(), content.createdAt) < \"00:01:00\" then '방금'
                    when timediff(now(), content.createdAt) < \"01:00:00\"
                        then concat(minute(timediff(now(), content.createdAt)), '분전')
                    else date_format(content.createdAt, \"%m/%d %H:%i\") end                           as writeDay,
                (select count(*) from contentLike where content.contentIdx = contentLike.contentIdx) as countLike,
                (select count(*) from comment where comment.contentIdx = content.contentIdx)         as countComment,
                (select count(*) from contentURL where contentURL.contentIdx = content.contentIdx)   as countImage
from user
         inner join comment using (userIdx)
         inner join content using (contentIdx)
         inner join notice using (noticeIdx)
where comment.userIdx = ?
group by comment.contentIdx
order by writeDay desc;
";
    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

//아직 적용안함
function getScrab($userIdx){
    $pdo=pdoSqlConnect();
    $query = "
select distinct content.contentIdx                                                                   as contentIdx,
                (case when content.userStatus = 0 then \"익명\" else user.userNickname end)              as contentWriter,
                content.contentTitle                                                                 as contentTitle,
                content.contentInf                                                                   as contentInf,
                noticeName                                                                           as noticeName,
                (case
                     when content.contentThumbnailURL is null then \"사진없음\"
                     else content.contentThumbnailURL end)                                           as contentThumbnailImage,
                case
                    when timediff(now(), content.createdAt) < \"00:01:00\" then '방금'
                    when timediff(now(), content.createdAt) < \"01:00:00\"
                        then concat(minute(timediff(now(), content.createdAt)), '분전')
                    else date_format(content.createdAt, \"%m/%d %H:%i\") end                           as writeDay,
                (select count(*) from contentLike where content.contentIdx = contentLike.contentIdx) as countLike,
                (select count(*) from comment where comment.contentIdx = content.contentIdx)         as countComment,
                (select count(*) from contentURL where contentURL.contentIdx = content.contentIdx)   as countImage
from user
         inner join comment using (userIdx)
         inner join content using (contentIdx)
         inner join notice using (noticeIdx)
         inner join scrab on scrab.userIdx = user.userIdx and scrab.contentIdx = content.contentIdx
where comment.userIdx = ?
group by comment.contentIdx
order by writeDay desc;
";
    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}
//아직 적용안함 -> 이거는 홈 화면에 핫게시물 네개만 뜨는거
function getHotContentHome(){
    $pdo=pdoSqlConnect();
    $query = "
select content.contentIdx                                                                   as contentIdx,
       content.contentTitle                                                                 as contentTitle,
       content.contentInf                                                                   as contentInf,
       noticeName                                                                           as noticeName,
       case
           when timediff(now(), content.createdAt) < \"00:01:00\" then '방금'
           when timediff(now(), content.createdAt) < \"01:00:00\"
               then concat(minute(timediff(now(), content.createdAt)), '분전')
           else date_format(content.createdAt, \"%m/%d %H:%i\") end                           as writeDay,
       (select count(*) from contentLike where content.contentIdx = contentLike.contentIdx) as countLike,
       (select count(*) from comment where comment.contentIdx = content.contentIdx)         as countComment
from user
         inner join content using (userIdx)
         inner join notice using (noticeIdx)
where (select count(*) from contentLike where content.contentIdx = contentLike.contentIdx) >= 10
order by content.createdAt desc
limit 4;
";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

//아직 적용안함 -> 이거는 홈화면에 실시간 인기글 2개만 뜨는거
function getPopularContentHome(){
    $pdo=pdoSqlConnect();
    $query = "
select (case when content.userStatus = 0 then \"익명\" else user.userNickname end)              as contentWriter,
       content.contentTitle                                                                 as contentTitle,
       content.contentInf                                                                   as contentInf,
       noticeName                                                                           as noticeName,
       date_format(content.createdAt, \"%m/%d %H:%i\")                                        as writeDay,
       (select count(*) from contentLike where content.contentIdx = contentLike.contentIdx) as countLike,
       (select count(*) from comment where comment.contentIdx = content.contentIdx)         as commentLike
from user
         inner join content using (userIdx)
         inner join notice using (noticeIdx)
where timediff(now(), content.createdAt) < \"24:00:00\"
order by 좋아요개수 desc
limit 2;
";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}