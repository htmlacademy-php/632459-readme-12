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

    $search_query = filter_input(INPUT_GET, 'search');
    if (!empty($search_query)) {
        header("Location: /search.php?search=$search_query");
    }

    $cur_page = filter_input(INPUT_GET, 'page');
    $page_items = 6;
    $sql_all_posts = 'SELECT COUNT(*) as count FROM posts WHERE repost IS NULL';
    $result = form_sql_request($con, $sql_all_posts, []);
    $items_count = mysqli_fetch_assoc($result)['count'];

    $pagination_info = getPaginationPages($items_count, $page_items, $cur_page);

    $pages = $pagination_info['pages'];
    $pages_count = $pagination_info['pages_count'];
    $offset = $pagination_info['offset'];

    $sql_types = 'SELECT id, type, name FROM content_types ORDER BY priority';
    $result = form_sql_request($con, $sql_types, []);
    $types = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $tab = filter_input(INPUT_GET, 'tab');

    $sql_filter = 'SELECT posts.*, login, avatar_path, class, type FROM posts '
        . 'JOIN users u ON user_id = u.id '
        . 'JOIN content_types c ON content_type = c.id '
        . 'WHERE repost IS NULL '
        . 'ORDER BY show_count DESC LIMIT ' . $page_items . ' OFFSET ' . $offset;

    $params = [];

    if ($tab) {
        $sql_all_posts = 'SELECT COUNT(*) as count FROM posts JOIN content_types c ON content_type = c.id WHERE repost IS NULL AND c.id = ?';
        $result = form_sql_request($con, $sql_all_posts, [$tab]);
        $items_count = mysqli_fetch_assoc($result)['count'];

        $pagination_info = getPaginationPages($items_count, $page_items, $cur_page);

        $pages = $pagination_info['pages'];
        $pages_count = $pagination_info['pages_count'];
        $offset = $pagination_info['offset'];

        $sql_filter = 'SELECT posts.*, login, avatar_path, class, type FROM posts '
        . 'JOIN users u ON user_id = u.id '
        . 'JOIN content_types c ON content_type = c.id '
        . 'WHERE c.id = ? AND repost IS NULL '
        . 'ORDER BY show_count DESC LIMIT ' . $page_items . ' OFFSET ' . $offset;
        $params = [$tab];
    }

    $result = form_sql_request($con, $sql_filter, $params);

    $popular_posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $popular_comments = [];
    $popular_likes = [];

    foreach ($popular_posts as $post) {
        $sql_comments = 'SELECT COUNT(id) AS total FROM comments '
        . 'WHERE post_id = '. $post['id'];
        $result = form_sql_request($con, $sql_comments, []);
        $comments = mysqli_fetch_all($result, MYSQLI_ASSOC);
        array_push($popular_comments, $comments[0]['total']);

        $sql_likes = 'SELECT COUNT(id) AS total FROM likes '
        . 'WHERE like_post_id = '. $post['id'];
        $result = form_sql_request($con, $sql_likes, []);
        $likes = mysqli_fetch_all($result, MYSQLI_ASSOC);
        array_push($popular_likes, $likes[0]['total']);
    }

    $page_content = include_template('popular.php', [
        'popular_posts' => $popular_posts,
        'types'         => $types,
        'tab'           => $tab,
        'popular_comments' => $popular_comments,
        'popular_likes' => $popular_likes,
        'pages_count' => $pages_count,
        'cur_page' => $cur_page,
        'pages' => $pages
    ]);

    $layout_content = include_template('layout.php', [
        'content'   => $page_content,
        'title'     => $page_titles['index']
    ]);

    print($layout_content);
