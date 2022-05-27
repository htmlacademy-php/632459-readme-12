<?php
    date_default_timezone_set('Europe/Moscow');

    require_once 'config/db.php';

    $con = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);
    mysqli_set_charset($con, "utf8");

    if (!isset($_SESSION['user'])) {
        session_start();
    }

    return $con;
