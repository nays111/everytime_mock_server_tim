<?php

function getClass($classIdx){
    $pdo=pdoSqlConnect();
    $query = "
select class.classIdx
     , className
     , concat(classYear, '년 ', classSemester, '학기')                                   as classSemester
     , classCode
     , professor
     , classGrade
     , classType
     , classPoint
     , classPeople
     , (select count(*) from myTimeTable where myTimeTable.classIdx = class.classIdx and myTimeTable.status=0) as timeTablePeople
     , (select majorName from major where major.majorIdx = class.majorIdx)            as major
from class
where classIdx = ? and class.status=0
;";


    $timeArray = Array();
    $st = $pdo->prepare($query);
    $st->execute([$classIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);

    while($row=$st->fetch()){
        $classIdx = $row['classIdx'];
        $row['time']= getClassesTime($classIdx);

        array_push($timeArray , $row);
    }
    $st = null;
    $pdo = null;

    return $timeArray;
}
//유효한 class index값인지 검사
function isValidClass($classIdx){
    $pdo=pdoSqlConnect();
$query = "SELECT EXISTS(SELECT * FROM class WHERE classIdx= ? and class.status=0) AS validClass;";
$st = $pdo -> prepare($query);
$st->execute([$classIdx]);
$st->setFetchMode(PDO::FETCH_ASSOC);
$res = $st->fetchAll();
$st=null;
$pdo = null;

return intval($res[0]["validClass"]);
}

function isValidClassComment($classCommentIdx){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM classComment WHERE classCommentIdx= ? and classComment.status=0) AS validClassComment;";
    $st = $pdo -> prepare($query);
    $st->execute([$classCommentIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validClassComment"]);
}

//이미 수강평에 좋아요 되있는지 확인하기 위해서
function isRedundantClassCommentLike($classCommentIdx,$userIdx){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM classCommentLike WHERE classCommentIdx= ? and userIdx= ?) AS redundantClassCommentLike;";
    $st = $pdo -> prepare($query);
    $st->execute([$classCommentIdx,$userIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["redundantClassCommentLike"]);
}
//내가 쓴 수강평인지 확인하기 위해서
function isMyClassComment($classCommentIdx){
    $pdo=pdoSqlConnect();
    $st = $pdo->prepare('select userIdx from classComment where classCommentIdx= :classCommentIdx;');
    $st->bindParam(':classCommentIdx', $classCommentIdx);
    $st->execute();
    $res = $st->fetch();
    $st = null;
    $pdo = null;
    return $res["userIdx"];
}

function getClasses(){
    $pdo=pdoSqlConnect();
    $query = "
select class.classIdx
     , className
     , classGrade
     , classType
     , concat(classPoint, \"학점\") as classPoint
     , classPeople
     , classCode
     , professor
     , truncate((select ifnull(avg(selectStar), 0) from classComment where classComment.classIdx = class.classIdx and classComment.status=0),
                0)              as classStar
     , (select count(*) from myTimeTable where myTimeTable.classIdx = class.classIdx and myTimeTable.status=0) as timeTablePeople               
from class
where class.status=0
order by classIdx
;";


    $timeArray = Array();
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);

    while($row=$st->fetch()){
        $classIdx = $row['classIdx'];
        $row['time']= getClassesTime($classIdx);

        array_push($timeArray , $row);
    }


    $st = null;
    $pdo = null;

    return $timeArray;
}
function getClassesByName($className){
    $pdo=pdoSqlConnect();
    $query = "
select class.classIdx
     , className
     , classGrade
     , classType
     , concat(classPoint, \"학점\") as classPoint
     , classPeople
     , classCode
     , professor
     , truncate((select ifnull(avg(selectStar), 0) from classComment where classComment.classIdx = class.classIdx and classComment.status=0),
                0)              as classStar
     , (select count(*) from myTimeTable where myTimeTable.classIdx = class.classIdx and myTimeTable.status=0) as timeTablePeople                
from class
where className like concat('%', ?, '%') and class.status=0
order by classIdx
;";


    $timeArray = Array();
    $st = $pdo->prepare($query);
    $st->execute([$className]);
    $st->setFetchMode(PDO::FETCH_ASSOC);

    while($row=$st->fetch()){
        $classIdx = $row['classIdx'];
        $row['time']= getClassesTime($classIdx);

        array_push($timeArray , $row);
    }

    $st = null;
    $pdo = null;

    return $timeArray;
}
function getClassesByProfessor($professor){
    $pdo=pdoSqlConnect();
    $query = "
select class.classIdx
     , className
     , classGrade
     , classType
     , concat(classPoint, \"학점\") as classPoint
     , classPeople
     , classCode
     , professor
     , truncate((select ifnull(avg(selectStar), 0) from classComment where classComment.classIdx = class.classIdx and classComment.status=0),
                0)              as classStar
     , (select count(*) from myTimeTable where myTimeTable.classIdx = class.classIdx and myTimeTable.status=0) as timeTablePeople                
from class
where professor like concat('%', ?, '%') and class.status=0
order by classIdx
;";


    $timeArray = Array();
    $st = $pdo->prepare($query);
    $st->execute([$professor]);
    $st->setFetchMode(PDO::FETCH_ASSOC);

    while($row=$st->fetch()){
        $classIdx = $row['classIdx'];
        $row['time']= getClassesTime($classIdx);

        array_push($timeArray , $row);
    }

    $st = null;
    $pdo = null;

    return $timeArray;
}
function getClassesByCode($code){
    $pdo=pdoSqlConnect();
    $query = "
select class.classIdx
     , className
     , classGrade
     , classType
     , concat(classPoint, \"학점\") as classPoint
     , classPeople
     , classCode
     , professor
     , truncate((select ifnull(avg(selectStar), 0) from classComment where classComment.classIdx = class.classIdx and classComment.status=0),
                0)              as classStar
     , (select count(*) from myTimeTable where myTimeTable.classIdx = class.classIdx and myTimeTable.status=0) as timeTablePeople                
from class
where classCode like concat('%', ?, '%') and class.status=0
order by classIdx
;";


    $timeArray = Array();
    $st = $pdo->prepare($query);
    $st->execute([$code]);
    $st->setFetchMode(PDO::FETCH_ASSOC);

    while($row=$st->fetch()){
        $classIdx = $row['classIdx'];
        $row['time']= getClassesTime($classIdx);

        array_push($timeArray , $row);
    }

    $st = null;
    $pdo = null;

    return $timeArray;
}
function getClassesByRoom($room){
    $pdo=pdoSqlConnect();
    $query = "
select class.classIdx
     , className
     , classGrade
     , classType
     , concat(classPoint, \"학점\") as classPoint
     , classPeople
     , classCode
     , professor
     , truncate((select ifnull(avg(selectStar), 0) from classComment where classComment.classIdx = class.classIdx and classComment.status=0),
                0)              as classStar
     , (select count(*) from myTimeTable where myTimeTable.classIdx = class.classIdx and myTimeTable.status=0) as timeTablePeople                
from class
where classRoom like concat('%', ?, '%') and class.status=0
order by classIdx
;";


    $timeArray = Array();
    $st = $pdo->prepare($query);
    $st->execute([$room]);
    $st->setFetchMode(PDO::FETCH_ASSOC);

    while($row=$st->fetch()){
        $classIdx = $row['classIdx'];
        $row['time']= getClassesTime($classIdx);

        array_push($timeArray , $row);
    }

    $st = null;
    $pdo = null;

    return $timeArray;
}


function getClassesTime($classIdx){
    $pdo=pdoSqlConnect();
    $query = "
select 
       concat(concat(classDay, concat((case
                                           when min(classTime) = 1 then \"09:00~\"
                                           when min(classTime) = 2 then \"10:00~\"
                                           when min(classTime) = 3 then \"11:00~\"
                                           when min(classTime) = 4 then \"12:00~\"
                                           when min(classTime) = 5 then \"13:00~\"
                                           when min(classTime) = 6 then \"14:00~\"
                                           when min(classTime) = 7 then \"15:00~\"
                                           when min(classTime) = 8 then \"16:00~\"
                                           when min(classTime) = 9 then \"17:00~\"
                                           when min(classTime) = 10 then \"18:00~\" end), (case
                                                                                             when max(classTime) = 1
                                                                                                 then \"10:00\"
                                                                                             when max(classTime) = 2
                                                                                                 then \"11:00\"
                                                                                             when max(classTime) = 3
                                                                                                 then \"12:00\"
                                                                                             when max(classTime) = 4
                                                                                                 then \"13:00\"
                                                                                             when max(classTime) = 5
                                                                                                 then \"14:00\"
                                                                                             when max(classTime) = 6
                                                                                                 then \"15:00\"
                                                                                             when max(classTime) = 7
                                                                                                 then \"16:00\"
                                                                                             when max(classTime) = 8
                                                                                                 then \"17:00\"
                                                                                             when max(classTime) = 9
                                                                                                 then \"18:00\"
                                                                                             when max(classTime) = 10
                                                                                                 then \"19:00\" end))),
              \" (\", classRoom, \")\") as classTime
from class
         inner join classTime using (classIdx)
where classIdx=? and class.status=0         
group by classIdx, classDay
order by classIdx, classDay desc;";
    $st = $pdo->prepare($query);
    $st->execute([$classIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;

}

function isValidClassName($name){
    $pdo = pdoSqlConnect();
    $query = "
        SELECT classIdx 
        FROM class 
        WHERE class.className like concat('%',?,'%') and class.status=0;";
    $st = $pdo->prepare($query);
    $st->execute([$name]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]);
}

function isValidClassProfessor($professor){
    $pdo = pdoSqlConnect();
    $query = "
        SELECT classIdx 
        FROM class 
        WHERE class.professor like concat('%',?,'%') and class.status=0;";
    $st = $pdo->prepare($query);
    $st->execute([$professor]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]);
}

function isValidClassNameAndProfessor($professor,$className){
    $pdo = pdoSqlConnect();
    $query = "
        SELECT classIdx 
        FROM class 
        WHERE class.professor like concat('%',?,'%') or class.className like concat('%',?,'%');";
    $st = $pdo->prepare($query);
    $st->execute([$professor,$className]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]);
}


function isValidClassCode($code){
    $pdo = pdoSqlConnect();
    $query = "
        SELECT classIdx 
        FROM class 
        WHERE class.classCode like concat('%',?,'%') and class.status=0;";
    $st = $pdo->prepare($query);
    $st->execute([$code]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]);
}

function isValidClassRoom($room){
    $pdo = pdoSqlConnect();
    $query = "
        SELECT classIdx 
        FROM class 
        WHERE class.classRoom like concat('%',?,'%') and class.status=0;";
    $st = $pdo->prepare($query);
    $st->execute([$room]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]);
}

function getNewClassComment(){
    $pdo=pdoSqlConnect();
    $query = "
select classComment.classCommentIdx,
       class.className,
       class.professor,
       classComment.classCommentInf,
       truncate((select ifnull(avg(selectStar), 0)
                 from classComment
                 where classComment.classIdx = class.classIdx
                   and classComment.status = 0),
                0) as classStar
from classComment
         inner join class using (classIdx)
where classComment.status = 0
  and class.status = 0
order by classComment.createdAt desc
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

//2월에서 8월 사이에는 1학기 시간표에 추가한 과목이 나타납니다, 9월에서 1월 사이에는 2학기 시간표에 추가한 과목이 나타납니다
function getMyClasses($userIdx){
    $pdo=pdoSqlConnect();
    $query = "
select class.classIdx, class.className, class.professor
from timeTable
         inner join myTimeTable using (timeTableIdx)
         inner join class using (classIdx)
where year(now()) = timeTable.year
  and (case when month(now()) between 2 and 8 then 1 else 0 end) = timeTable.semester
  and timeTable.userIdx = ?
  and timeTable.status = 0 and myTimeTable.status=0
  and class.status = 0;
";
    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getClassComments(){
    $pdo=pdoSqlConnect();
    $query = "
select classComment.classCommentIdx,
       class.className,
       class.professor,
       classComment.classCommentInf,
       concat(right(class.classYear, 2), '년', class.classSemester, '학기 수강자')                                         as classStudent,
       truncate((select ifnull(avg(selectStar), 0)
                 from classComment
                 where classComment.classIdx = class.classIdx
                   and classComment.status = 0),
                0)                                                                                                   as classStar,
       (select count(*)
        from classCommentLike
        where classCommentLike.classCommentIdx = classComment.classCommentIdx)                                       as classCommentLike
from classComment
         inner join class using (classIdx)
where classComment.createdAt =
      (select max(t1.createdAt) from classComment as t1 where t1.classIdx = classComment.classIdx)
  and classComment.status = 0
  and class.status = 0
order by classComment.createdAt desc;
";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}


function getClassList($keyword){
    $pdo=pdoSqlConnect();
    $query = "
select distinct class.classIdx,
                class.className,
                class.professor,
                truncate((select ifnull(avg(selectStar), 0)
                          from classComment
                          where classComment.classIdx = class.classIdx),
                         0) as classStar
from class
         left outer join classComment using (classIdx)
where class.className like concat('%', ?, '%')
   or class.professor like concat('%', ?, '%')
order by classStar desc;
";
    $st = $pdo->prepare($query);
    $st->execute([$keyword,$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}



//게시글 작성
function postClassComment($userIdx,$classIdx,$selectStar,$selectHw,$selectTeam,$selectRate,$selectAtt,$selectTest,$selectSemester,$classCommentInf){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO classComment(userIdx,classIdx,selectStar,selectHw,selectTeam,selectRate,selectAtt,selectTest,selectSemester,classCommentInf) VALUES (?,?,?,?,?,?,?,?,?,?)";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx,$classIdx,$selectStar,$selectHw,$selectTeam,$selectRate,$selectAtt,$selectTest,$selectSemester,$classCommentInf]);

    $st = null;
    $pdo = null;
}



function isRedundantClassComment($userIdx,$classIdx){
    $pdo = pdoSqlConnect();
    $query = "
        SELECT EXISTS(SELECT * 
        FROM classComment 
        WHERE classComment.userIdx=? and classComment.classIdx=? and classComment.status=0) AS redundantClassComment";
    $st = $pdo->prepare($query);
    $st->execute([$userIdx,$classIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["redundantClassComment"]);
}

function postClassCommentLike($userIdx,$classCommentIdx){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO classCommentLike(userIdx, classCommentIdx) VALUES (?,?)";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx,$classCommentIdx]);

    $st = null;
    $pdo = null;
}

function getTimeTable($timeTableIdx,$userIdx){
    $pdo=pdoSqlConnect();
    $query = "
select class.classIdx,
       class.className,
       class.classRoom,
       (case
            when classTime.classDay = \"월\" then \"mon\"
            when classTime.classDay = \"화\" then \"tue\"
            when classTime.classDay = \"수\" then \"wed\"
            when classTime.classDay = \"목\" then \"thu\"
            when classTime.classDay = \"금\" then \"fri\"
           end) as classDay,
       classTime.classTime
from timeTable
         inner join myTimeTable using (timeTableIdx)
         inner join class using (classIdx)
         inner join classTime using (classIdx)
where timeTableIdx = ?
  and myTimeTable.userIdx = ?
  and timeTable.status = 0
  and myTimeTable.status = 0;

";
    $st = $pdo->prepare($query);
    $st->execute([$timeTableIdx,$userIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getMyTimeTableList($userIdx){
    $pdo=pdoSqlConnect();
    $query = "
select timeTableIdx, concat(year, \"년 \", semester, \"학기\") as timeTableYear, name as timeTableName
from timeTable
where userIdx = ?
  and status = 0
";
    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getMyTimeTableInf($userIdx,$timeTableIdx){
    $pdo=pdoSqlConnect();
    $query = "
select  concat(year, \"년 \", semester, \"학기\") as timeTableYear, name as timeTableName
from timeTable
where userIdx = ? and timeTableIdx=?
  and status = 0
";
    $st = $pdo->prepare($query);
    $st->execute([$userIdx,$timeTableIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}



function isValidTimeTable($timeTableIdx){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM timeTable WHERE timeTableIdx= ? and timeTable.status=0) AS validTimeTable;";
    $st = $pdo -> prepare($query);
    $st->execute([$timeTableIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validTimeTable"]);
}

function postMyTimeTable($userIdx,$timeTableIdx,$classIdx){
        $pdo = pdoSqlConnect();
        $query = "INSERT INTO myTimeTable(userIdx,timeTableIdx,classIdx) VALUES (?,?,?)";

        $st = $pdo->prepare($query);
        $st->execute([$userIdx,$timeTableIdx,$classIdx]);

        $st = null;
        $pdo = null;
}

//내 시간표 이미 수업들어있는지 확인하는 함수
function isRedundantClassInMyTimeTable($timeTableIdx,$classIdx){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM myTimeTable WHERE timeTableIdx= ? and classIdx=? and status=0) AS redundantClassInMyTimeTable;";
    $st = $pdo -> prepare($query);
    $st->execute([$timeTableIdx,$classIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["redundantClassInMyTimeTable"]);
}

//내 시간표에 있는 수업과 새로 추가할 수업간 시간 겹치는지 확인하기 위한 함수
function isRedundantClassTimeMyTimeTable($classIdx,$userIdx,$timeTableIdx){
    $pdo=pdoSqlConnect();
    $query = "
select exists(select t1.className, t1.classDay, t1.classTime, t2.className, t2.classDay, t2.classTime
              from (select classIdx, className, classDay, classTime
                    from class
                             inner join classTime using (classIdx)
                    where classIdx = ?
                      and class.status = 0) as t1
                       inner join (select classIdx, className, classDay, classTime
                                   from myTimeTable
                                            inner join class using (classIdx)
                                            inner join classTime using (classIdx)
                                   where myTimeTable.userIdx = ?
                                     and myTimeTable.status = 0
                                     and myTimeTable.timeTableIdx = ?) as t2 using (classDay, classTime)) AS redundantClassTimeMyTimeTable;    
    ";
    $st = $pdo -> prepare($query);
    $st->execute([$classIdx,$userIdx,$timeTableIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["redundantClassTimeMyTimeTable"]);
}


function deleteClassInMyTimeTable($userIdx,$classIdx,$timeTableIdx){
    $pdo = pdoSqlConnect();
    $query = "UPDATE myTimeTable set status = 1 where userIdx=? and classIdx=? and timeTableIdx=?";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx,$classIdx,$timeTableIdx]);

    $st = null;
    $pdo = null;
}


function isValidClassInMyTimeTable($classIdx){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM myTimeTable WHERE classIdx=? and status=0) AS validClassInMyTimeTable;";
    $st = $pdo -> prepare($query);
    $st->execute([$classIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validClassInMyTimeTable"]);
}


//수강평 요약 정보
function getSummaryOfClassComment($classIdx){
    $pdo=pdoSqlConnect();
    $query = "
select distinct (truncate(
        (select ifnull(avg(selectStar), 0) from classComment where classComment.classIdx = class.classIdx),
        0))               as classStar,
                (select selectHw
                 from classComment
                 where classIdx = class.classIdx
                 group by selectHw
                 order by count(selectHw) desc
                 limit 1) as selectHw,
                (select selectTeam
                 from classComment
                 where classIdx = class.classIdx
                 group by selectTeam
                 order by count(selectTeam) desc
                 limit 1) as selectTeam,
                (select selectRate
                 from classComment
                 where classIdx = class.classIdx
                 group by selectRate
                 order by count(selectRate) desc
                 limit 1) as selectRate,
                (select selectAtt
                 from classComment
                 where classIdx = class.classIdx
                 group by selectAtt
                 order by count(selectAtt) desc
                 limit 1) as selectAtt,
                (select selectTest
                 from classComment
                 where classIdx = class.classIdx
                 group by selectTest
                 order by count(selectTest) desc
                 limit 1) as selectTest
from class
         inner join classComment using (classIdx)
where class.classIdx = ? and class.status = 0 and classComment.status = 0
";
    $st = $pdo->prepare($query);
    $st->execute([$classIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}


function getDistinctClassComment($classIdx){

    $pdo=pdoSqlConnect();
    $query = "
select classComment.classCommentIdx,
       classComment.classCommentInf,
       concat(right(class.classYear, 2), '년', class.classSemester, '학기 수강자')                                         as classStudent,
       truncate((select ifnull(avg(selectStar), 0)
                 from classComment
                 where classComment.classIdx = class.classIdx
                   and classComment.status = 0),
                0)                                                                                                   as classStar,
       (select count(*)
        from classCommentLike
        where classCommentLike.classCommentIdx = classComment.classCommentIdx)                                       as classCommentLike
from classComment
         inner join class using (classIdx)
where classComment.createdAt =
      (select max(t1.createdAt) from classComment as t1 where t1.classIdx = classComment.classIdx)
  and classComment.status = 0
  and class.status = 0 and classIdx=?
order by classComment.createdAt desc;
";
    $st = $pdo->prepare($query);
    $st->execute([$classIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}