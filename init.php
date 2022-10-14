<?php

date_default_timezone_set('Europe/Moscow');

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
ini_set('error_log', __DIR__ . '/php-errors.log');

require_once 'config/db.php';

$con = mysqli_connect(
    $db['host'],
    $db['user'],
    $db['password'],
    $db['database']
);
mysqli_set_charset($con, "utf8");

if (session_id() == '' || !isset($_SESSION)
    || session_status() === PHP_SESSION_NONE
) {
    session_start();
}

return $con;
