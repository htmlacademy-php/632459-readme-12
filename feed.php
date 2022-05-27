<?php
    require_once 'init.php';
    require_once 'helpers.php';
    require_once 'functions.php';

    if (!$_SESSION['user']) {
        header("Location: /");
        exit();
    }

    [$is_auth, $user_name, $page_titles] = require('data.php');
    $con = require('init.php');

    if (!$con) {
        $error = mysqli_connect_error();
        print("Ошибка подключения: " . $error);
        die();
    }

    $params = [];

    $sql_types = 'SELECT id, type, name FROM content_types ORDER BY priority';
    $result = form_sql_request($con, $sql_types, []);
    $types = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $tab = filter_input(INPUT_GET, 'tab');
    $user_id = filter_input(INPUT_GET, 'user');

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

    $feed_hashtags = [];

    foreach ($posts as $post) {
        $sql_hashtags = 'SELECT hashtag_name FROM posts p '
        . 'JOIN post_tags pt ON p.id=pt.post_id '
        . 'JOIN hashtags h ON pt.hashtag_id=h.id '
        . 'WHERE p.id = ?';

        $result = form_sql_request($con, $sql_hashtags, [$post['id']]);

        $hashtags = mysqli_fetch_all($result, MYSQLI_ASSOC);

        $feed_hashtags[$post['id']] = $hashtags;
    }

    $page_content = include_template('feed.php', [
        'posts' => $posts,
        'types' => $types,
        'tab' => $tab,
        'feed_hashtags' => $feed_hashtags
    ]);

    $layout_content = include_template('layout.php', [
        'content'   => $page_content,
        'title'     => $page_titles['feed']
    ]);

    print($layout_content);
