<?php
    require_once 'init.php';
    require_once 'helpers.php';
    require_once 'functions.php';

    [$is_auth, $user_name, $page_titles] = require('data.php');
    $con = require('init.php');

    if (!$con) {
        $error = mysqli_connect_error();
        print("Ошибка подключения: " . $error);
        die();
    }

    $user_id = 2;
    $params = [];

    $sql_types = 'SELECT id, type, name FROM content_types ORDER BY priority';
    $result = form_sql_request($con, $sql_types, []);
    $types = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $tab = filter_input(INPUT_GET, 'tab');

    $sql_feed = 'SELECT p.*, type, class, login, avatar_path FROM subscriptions' .
    ' JOIN posts p ON p.user_id = subscribe_id' .
    ' JOIN users u ON p.user_id = u.id ' .
    ' JOIN content_types c ON content_type = c.id' .
    ' WHERE follower_id = ' . $user_id .
    ' ORDER BY date_add DESC';

    if ($tab) {
        $sql_feed = 'SELECT p.*, type, class, login, avatar_path FROM subscriptions' .
        ' JOIN posts p ON p.user_id = subscribe_id' .
        ' JOIN users u ON p.user_id = u.id ' .
        ' JOIN content_types c ON content_type = c.id' .
        ' WHERE follower_id = ' . $user_id . ' AND c.id = ?' .
        ' ORDER BY date_add DESC';
        $params = [$tab];
    }

    $result = form_sql_request($con, $sql_feed, $params);

    $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $page_content = include_template('feed.php', [
        'posts' => $posts,
        'types' => $types,
        'tab' => $tab
    ]);

    $layout_content = include_template('layout.php', [
        'content'   => $page_content,
        'title'     => $page_titles['feed'],
        'user_name' => $user_name,
        'is_auth' => $is_auth
    ]);

    print($layout_content);
