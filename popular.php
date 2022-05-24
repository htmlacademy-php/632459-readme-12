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

    $sql_types = 'SELECT id, type, name FROM content_types ORDER BY priority';
    $result = form_sql_request($con, $sql_types, []);
    $types = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $tab = filter_input(INPUT_GET, 'tab');

    $sql_filter = 'SELECT posts.*, login, avatar_path, class, type FROM posts '
        . 'JOIN users u ON user_id = u.id '
        . 'JOIN content_types c ON content_type = c.id '
        . 'ORDER BY show_count DESC';

    $params = [];

    if ($tab) {
        $sql_filter = 'SELECT posts.*, login, avatar_path, class, type FROM posts '
        . 'JOIN users u ON user_id = u.id '
        . 'JOIN content_types c ON content_type = c.id '
        . 'WHERE c.id = ? '
        . 'ORDER BY show_count DESC';
        $params = [$tab];
    }

    $result = form_sql_request($con, $sql_filter, $params);

    $popular_posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $page_content = include_template('popular.php', [
        'popular_posts' => $popular_posts,
        'types'         => $types,
        'tab'           => $tab
    ]);

    $layout_content = include_template('layout.php', [
        'content'   => $page_content,
        'title'     => $page_titles['index'],
        'user_name' => $user_name,
        'is_auth'   => $is_auth
    ]);

    print($layout_content);
