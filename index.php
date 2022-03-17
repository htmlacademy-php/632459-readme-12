<?php
    require_once 'init.php';
    require_once 'helpers.php';
    require_once 'functions.php';
    require_once 'data.php';

    if (!$con) {
        $error = mysqli_connect_error();
        print("Ошибка подключения: " . $error);
    } else {
        $sql = 'SELECT type, name FROM content_types ORDER BY priority';
        $result = mysqli_query($con, $sql);

        if ($result) {
            $types = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $error = mysqli_error($con);
            print("Ошибка подключения: " . $error);
        }

        $sql = 'SELECT posts.*, login, avatar_path, class FROM posts
        JOIN users u ON user_id = u.id
        JOIN content_types c ON content_type = c.id
        ORDER BY show_count DESC';

        $result = mysqli_query($con, $sql);

        if ($result) {
            $popular_posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $error = mysqli_error($con);
            print("Ошибка подключения: " . $error);
        }
    }

    $page_content = include_template('main.php', [
        'popular_posts' => $popular_posts,
		'types' => $types
    ]);

    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'title' => $page_titles['index'],
        'user_name' => $user_name,
        'is_auth' => $is_auth
    ]);

    print($layout_content);
