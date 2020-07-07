<?php
require './pdos/DatabasePdo.php';
require './pdos/IndexPdo.php';
require './pdos/NoticePdo.php';
require './pdos/TimetablePdo.php';
require './vendor/autoload.php';

use \Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;

date_default_timezone_set('Asia/Seoul');
ini_set('default_charset', 'utf8mb4');

//에러출력하게 하는 코드
//error_reporting(E_ALL); ini_set("display_errors", 1);

//Main Server API
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    /* ******************   Test   ****************** */
    $r->addRoute('GET', '/', ['IndexController', 'index']);
    $r->addRoute('GET', '/test', ['IndexController', 'test']);
    $r->addRoute('GET', '/test/{testNo}', ['IndexController', 'testDetail']);
    $r->addRoute('POST', '/test', ['IndexController', 'testPost']);
    $r->addRoute('GET', '/jwt', ['MainController', 'validateJwt']);
    $r->addRoute('POST', '/jwt', ['MainController', 'createJwt']);


    /* ******************   EveryTime   ****************** */

    /* ************************************************* 로그인 관련 기능 **************************************************** */
    $r->addRoute('POST','/user',['IndexController','postUser']); //회원가입 API
    $r->addRoute('POST','/login',['IndexController','login']); //로그인 API
    $r->addRoute('GET','/user',['IndexController','getUser']); //유저 정보 조회 API
    $r->addRoute('PATCH','/user',['IndexController','updateUser']); //유저 정보 변경 API (닉네임만 변경할 수 있음)
    $r->addRoute('DELETE','/user',['IndexController','deleteUser']); //회원 탈퇴 API

    /* ************************************************* 광고 관련 기능 **************************************************** */
    $r->addRoute('GET','/ads',['IndexController','getAds']); //광고 리스트 조회 API

    /* ************************************************* 게시판 관련 기능 **************************************************** */
    /* ****************** 홈 화면 관련 API *************** */
    $r->addRoute('GET', '/myNotice', ['NoticeController', 'getMyNotice']); //즐겨찾기 게시판 리스트 조회 API (홈화면)
    $r->addRoute('GET','/hot-content',['NoticeController','getHotContentHome']); // 핫 게시물 조회 API (홈화면)
    $r->addRoute('GET','/popular-content',['NoticeController','getPopularContentHome']); // 실시간 인기글 조회 API (홈화면)
    $r->addRoute('GET', '/notice-list', ['NoticeController', 'getNoticeList']); //전체 게시판 리스트 조회 API
    $r->addRoute('POST', '/myNotice', ['NoticeController', 'postMyNotice']); // 즐겨찾기 게시판 추가/취소 API
    $r->addRoute('GET', '/notice/{noticeIdx}/contents', ['NoticeController', 'getContentsByNotice']); //특정 게시판에서 컨텐츠(게시물) 리스트 조회 API -> 검색
    $r->addRoute('GET', '/notice/contents', ['NoticeController', 'getContents']); // 전체 게시판에서 컨텐츠(게시물) 리스트 조회 API-> 내가 쓴 글 조회,댓글 단 글 조회, 스크랩한 글 조회, 핫 게시물 조회, 검색

    /* ******************* 컨텐츠(게시물) 관련 API *************** */
    $r->addRoute('GET', '/notice/content/{contentIdx}', ['NoticeController', 'getContent']); // 컨텐츠(게시물) 조회 API
    $r->addRoute('POST', '/notice/{noticeIdx}/content', ['NoticeController', 'postContent']); // 컨텐츠(게시물) 작성 API
    $r->addRoute('PATCH', '/notice/content/{contentIdx}', ['NoticeController', 'updateContent']); // 컨텐츠(게시물) 수정 API
    $r->addRoute('DELETE', '/notice/content/{contentIdx}', ['NoticeController', 'deleteContent']); // 컨텐츠(게시물) 삭제 API
    $r->addRoute('POST', '/scrab', ['NoticeController', 'postScrab']); // 컨텐츠(게시물) 스크랩 추가/취소 API
    $r->addRoute('POST', '/contentLike', ['NoticeController', 'postContentLike']); // 컨텐츠(게시물) 좋아요 추가 API

    /* ******************* 댓글 관련 API *********************** */
    $r->addRoute('GET', '/notice/content/{contentIdx}/comments', ['NoticeController', 'getComments']); //댓글 조회 API
    $r->addRoute('POST', '/notice/content/{contentIdx}/comment', ['NoticeController', 'postComment']); // 댓글 작성 API
    $r->addRoute('DELETE', '/notice/content/comment/{commentIdx}', ['NoticeController', 'deleteComment']); // 댓글 삭제 API
    $r->addRoute('POST', '/commentLike', ['NoticeController', 'postCommentLike']); // 댓글 좋아요 추가 API


    /* ************************************************* 시간표 관련 기능 **************************************************** */
    /* *******************강좌 및 강의평 관련 API **************** */
    $r->addRoute('GET', '/class', ['TimetableController', 'getClasses']); //전체 강좌 조회 API
    $r->addRoute('GET', '/class/{classIdx}', ['TimetableController', 'getClass']); //특정 강좌 조회 API
    $r->addRoute('GET', '/new-classComment', ['TimetableController', 'getNewClassComment']); //최근 강의평 조회 API(홈화면)
    $r->addRoute('GET', '/myClass', ['TimetableController', 'getMyClasses']); //시간표에 추가한 강좌 리스트 조회 API
    $r->addRoute('GET', '/classComment', ['TimetableController', 'getClassComments']); //강의평 리스트 조회 API
    $r->addRoute('GET', '/class-list', ['TimetableController', 'getClassList']); //강좌 리스트 조회 API
    $r->addRoute('GET', '/classComment/{classIdx}', ['TimetableController', 'getClassComment']); //강의평 상세 조회 API     -> 아직 못함

    $r->addRoute('POST', '/class/{classIdx}/classComment', ['TimetableController', 'postClassComment']); // 강의평 작성 API
    $r->addRoute('POST', '/classCommentLike', ['TimetableController', 'postClassCommentLike']); // 강의평 좋아요 추가 API



    /* *******************시간표 관련 API **************** */
    $r->addRoute('GET', '/timeTable', ['TimetableController', 'getTimeTableList']); //내 시간표 목록 조회 API
    $r->addRoute('GET', '/timeTable/{timeTableIdx}', ['TimetableController', 'getTimeTable']); //내 시간표 조회 API
    $r->addRoute('POST', '/timeTable/{timeTableIdx}/class', ['TimetableController', 'postMyTimeTable']); // 시간표에 수업 추가 API






//    $r->addRoute('GET', '/users', 'get_all_users_handler');
//    // {id} must be a number (\d+)
//    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
//    // The /{title} suffix is optional
//    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// 로거 채널 생성
$accessLogs = new Logger('ACCESS_LOGS');
$errorLogs = new Logger('ERROR_LOGS');
// log/your.log 파일에 로그 생성. 로그 레벨은 Info
$accessLogs->pushHandler(new StreamHandler('logs/access.log', Logger::INFO));
$errorLogs->pushHandler(new StreamHandler('logs/errors.log', Logger::ERROR));
// add records to the log
//$log->addInfo('Info log');
// Debug 는 Info 레벨보다 낮으므로 아래 로그는 출력되지 않음
//$log->addDebug('Debug log');
//$log->addError('Error log');

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        echo "404 Not Found";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        echo "405 Method Not Allowed";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        switch ($routeInfo[1][0]) {
            case 'IndexController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/IndexController.php';
                break;
            case 'MainController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/MainController.php';
                break;
            case 'NoticeController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/NoticeController.php';
                break;
            case 'TimetableController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/TimetableController.php';
                break;
            /*case 'ProductController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ProductController.php';
                break;
            case 'SearchController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/SearchController.php';
                break;
            case 'ReviewController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ReviewController.php';
                break;
            case 'ElementController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ElementController.php';
                break;
            case 'AskFAQController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/AskFAQController.php';
                break;*/
        }

        break;
}
