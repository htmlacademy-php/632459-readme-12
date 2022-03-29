<?php
    require_once 'init.php';
    require_once 'helpers.php';
    require_once 'functions.php';
    require_once 'data.php';

    if (!$con) {
        $error = mysqli_connect_error();
        print("Ошибка подключения: " . $error);
        die();
    }

    $sql_types = 'SELECT id, type, name FROM content_types ORDER BY priority';
    $result = mysqli_query($con, $sql_types);

    if (!$result) {
        $error = mysqli_error($con);
        print("Ошибка подключения: " . $error);
        die();
    }

    $types = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $tab = filter_input(INPUT_GET, 'tab');

    $sql_filter = 'SELECT posts.*, login, avatar_path, class, type FROM posts '
        . 'JOIN users u ON user_id = u.id '
        . 'JOIN content_types c ON content_type = c.id '
        . 'ORDER BY show_count DESC';

    $stmt = db_get_prepare_stmt($con, $sql_filter);

    if ($tab) {
        $sql_filter = 'SELECT posts.*, login, avatar_path, class, type FROM posts '
        . 'JOIN users u ON user_id = u.id '
        . 'JOIN content_types c ON content_type = c.id '
        . 'WHERE c.id = ? '
        . 'ORDER BY show_count DESC';
        $stmt = db_get_prepare_stmt($con, $sql_filter, [$tab]);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        $error = mysqli_error($con);
        print("Ошибка подключения: " . $error);
        die();
    }

    $popular_posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $page_content = include_template('main.php', [
        'popular_posts' => $popular_posts,
		'types' => $types,
        'tab' => $tab
    ]);

    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'title' => $page_titles['index'],
        'user_name' => $user_name,
        'is_auth' => $is_auth,
        'class' => $main_classes['index']
    ]);

    print($layout_content);
