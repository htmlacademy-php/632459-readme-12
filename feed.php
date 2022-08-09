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

    $sql_types = 'SELECT id, type, name FROM content_types ORDER BY priority';
    $result = form_sql_request($con, $sql_types, []);
    $types = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $tab = filter_input(INPUT_GET, 'tab');
    $user_id = filter_input(INPUT_GET, 'user');

    $sql_feed = 'SELECT p.*, type, class, login, avatar_path,
    (SELECT COUNT(comment.id) FROM comments AS comment WHERE comment.post_id = p.id) AS comments_count,
    (SELECT COUNT(liked.id) FROM likes AS liked WHERE liked.like_post_id = p.id) AS likes_count
        FROM subscriptions
        JOIN posts p ON p.user_id = subscribe_id
        JOIN users u ON p.user_id = u.id
        JOIN comments com ON com.post_id = (p.id OR NULL)
        JOIN content_types c ON content_type = c.id
        WHERE follower_id = ?
        GROUP BY p.id
        ORDER BY date_add DESC';

    $params = [$user_id];

    if ($tab) {
    $sql_feed = 'SELECT p.*, type, class, login, avatar_path,
    (SELECT COUNT(comment.id) FROM comments AS comment WHERE comment.post_id = p.id) AS comments_count,
    (SELECT COUNT(liked.id) FROM likes AS liked WHERE liked.like_post_id = p.id) AS likes_count
        FROM subscriptions
        JOIN posts p ON p.user_id = subscribe_id
        JOIN users u ON p.user_id = u.id
        JOIN comments com ON com.post_id = (p.id OR NULL)
        JOIN content_types c ON content_type = c.id
        WHERE follower_id = ? AND c.id = ?
        GROUP BY p.id
        ORDER BY date_add DESC';

        $params = [$user_id, $tab];
    }

    $result = form_sql_request($con, $sql_feed, $params);

    $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $feed_hashtags = [];

    foreach ($posts as $post) {
        $sql_hashtags = 'SELECT hashtag_name FROM posts p
        JOIN post_tags pt ON p.id=pt.post_id
        JOIN hashtags h ON pt.hashtag_id=h.id
        WHERE p.id = ?';

        $result = form_sql_request($con, $sql_hashtags, [$post['id']]);
        $hashtags = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $feed_hashtags[$post['id']] = $hashtags;
    }

    $sql_reposts = 'SELECT id, (SELECT COUNT(*) FROM posts p WHERE p.parent_id = posts.id GROUP BY p.parent_id) AS repost_count FROM posts';
    $result = form_sql_request($con, $sql_reposts, []);
    $reposts = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $page_content = include_template('feed.php', [
        'posts' => $posts,
        'types' => $types,
        'tab' => $tab,
        'feed_hashtags' => $feed_hashtags,
        'reposts' => $reposts,
    ]);

    $layout_content = include_template('layout.php', [
        'content'   => $page_content,
        'title'     => $page_titles['feed']
    ]);

    print($layout_content);
