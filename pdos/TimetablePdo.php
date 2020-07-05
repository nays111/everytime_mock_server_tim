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
                2)              as classStar
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
                2)              as classStar
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
                2)              as classStar
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
                2)              as classStar
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
                2)              as classStar
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
select 
       classComment.classCommentIdx,
       class.className,
       class.professor,
       classComment.classCommentInf,
       truncate((select ifnull(avg(selectStar), 0) from classComment where classComment.classIdx = class.classIdx and classComment.status=0),
                2) as classStar
from classComment
         inner join class using (classIdx)
where classComment.status=0 and class.status=0
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
select class.classIdx,class.className, class.professor
from myTimeTable
         inner join class using (classIdx)
where year(now()) = myTimeTable.year
  and (case when month(now()) between 2 and 8 then 1 else 0 end) = myTimeTable.semester
  and myTimeTable.userIdx = ? and myTimeTable.status=0 and class.status=0;
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
       concat(right(class.classYear, 2), '년', class.classSemester, '학기 수강자') as classStudent,
       truncate((select ifnull(avg(selectStar), 0) from classComment where classComment.classIdx = class.classIdx and classComment.status=0),
                2)                                                  as classStar
from classComment
         inner join class using (classIdx)
where classComment.createdAt =
      (select max(t1.createdAt) from classComment as t1 where t1.classIdx = classComment.classIdx)
      and classComment.status=0 and class.status=0
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
                         2) as classStar
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


function isRedundandClassComment($userIdx,$classIdx){
    $pdo = pdoSqlConnect();
    $query = "
        SELECT classCommentIdx 
        FROM classComment 
        WHERE classComment.userIdx=? and classComment.classIdx=? and classComment.status=0;";
    $st = $pdo->prepare($query);
    $st->execute([$userIdx,$classIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]);
}