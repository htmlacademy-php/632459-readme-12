<?php
    require_once 'init.php';
    require_once 'helpers.php';
    require_once 'functions.php';

    $con = require('init.php');

    if (!$con) {
        $error = mysqli_connect_error();
        print("Ошибка подключения: " . $error);
        die();
    }

    $user_id = filter_input(INPUT_GET, 'user');

    $is_subscribe = false;

    $sql_subscriber = 'SELECT subscribe_id FROM subscriptions WHERE subscribe_id = ? AND follower_id = ?';
    $result = form_sql_request($con, $sql_subscriber, [$user_id, $_SESSION['user']['id']]);

    if (mysqli_num_rows($result) > 0) {
        $is_subscribe = true;
    }

    if ($user_id && !$is_subscribe) {
        $sql_subscribe = 'INSERT INTO subscriptions(subscribe_id, follower_id) VALUES(?, ' . $_SESSION['user']['id'] . ')';
        $result = form_sql_request($con, $sql_subscribe, [$user_id], false);
        header("Location: profile.php?user=" . $user_id . "&tab=posts");
    }

    if($user_id && $is_subscribe) {
        $sql_subscribe = 'DELETE FROM subscriptions WHERE subscribe_id = ? AND follower_id = ?';
        $result = form_sql_request($con, $sql_subscribe, [$user_id, $_SESSION['user']['id']], false);
        header("Location: profile.php?user=" . $user_id . "&tab=posts");
    }


