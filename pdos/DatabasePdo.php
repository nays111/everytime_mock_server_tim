
<?php

//DB 정보
function pdoSqlConnect()
{
    try {
        $DB_HOST = "3.34.118.117";
        $DB_NAME = "everyTimeDB";
        $DB_USER = "root";
        $DB_PW = "skdbstn15!";
        $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PW);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
}
