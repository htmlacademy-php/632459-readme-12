<?php
    require_once 'init.php';
    require_once 'helpers.php';
    require_once 'functions.php';

    [$is_auth, $user_name, $page_titles, $validate_rules, $input_names] = require('data.php');
    $con = require('init.php');

    if (!$con) {
        $error = mysqli_connect_error();
        print("Ошибка подключения: " . $error);
        die();
    }

    $user_id = filter_input(INPUT_GET, 'user');

    $sql_user = 'SELECT id, dt_reg, login, avatar_path FROM users '
    . 'WHERE id = ?';

    $result = form_sql_request($con, $sql_user, [$user_id]);
    $user = mysqli_fetch_array($result);

    $sql_subscribers = 'SELECT COUNT(follower_id) AS total FROM subscriptions '
    . 'JOIN users u ON u.id = subscribe_id '
    . 'WHERE u.id = ?';

    $result = form_sql_request($con, $sql_subscribers, [$user_id]);

    $subscribers = mysqli_fetch_array($result);

    $sql_publications = 'SELECT COUNT(id) AS total FROM posts '
    . 'WHERE user_id = ?';

    $result = form_sql_request($con, $sql_publications, [$user_id]);

    $publications = mysqli_fetch_array($result);

    $is_subscribe = false;

    $sql_subscribe = 'SELECT subscribe_id FROM subscriptions WHERE subscribe_id = ? AND follower_id = ?';
    $result = form_sql_request($con, $sql_subscribe, [$user_id, $_SESSION['user']['id']]);

    if (mysqli_num_rows($result)) {
        $is_subscribe = true;
    }

    $sql_posts = 'SELECT posts.*, type, class FROM posts'
    . ' JOIN content_types c ON content_type = c.id'
    . ' WHERE user_id = ?'
    . ' ORDER BY date_add DESC';

    $result = form_sql_request($con, $sql_posts, [$user_id]);

    $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $post_hashtags = [];
    $post_likes = [];
    $repost_info = [];

    foreach ($posts as $post) {
        $sql_hashtags = 'SELECT hashtag_name FROM posts p '
        . 'JOIN post_tags pt ON p.id=pt.post_id '
        . 'JOIN hashtags h ON pt.hashtag_id=h.id '
        . 'WHERE p.id = ? '
        . 'ORDER BY date_add DESC';

        $result = form_sql_request($con, $sql_hashtags, [$post['id']]);
        $hashtags = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $post_hashtags[$post['id']] = $hashtags;

        $sql_likes = 'SELECT COUNT(id) AS total FROM likes '
        . 'WHERE like_post_id = '. $post['id'];
        $result = form_sql_request($con, $sql_likes, []);
        $likes = mysqli_fetch_all($result, MYSQLI_ASSOC);
        array_push($post_likes, $likes[0]['total']);

        if ($post['repost']) {
            $sql_post_author = 'SELECT p.id, login, avatar_path FROM posts p '
            . 'JOIN users u ON p.original_author = u.id '
            . 'WHERE u.id = ?';
            $result = form_sql_request($con, $sql_post_author, [$post['original_author']]);

            $repost = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $repost_info[$post['id']] = $repost[0];
        }
    }



    $sql_reposts = 'SELECT id, (SELECT COUNT(*) FROM posts p WHERE p.parent_id = posts.id GROUP BY p.parent_id) AS repost_count FROM posts';
    $result = form_sql_request($con, $sql_reposts, []);
    $reposts = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $page_content = include_template('profile.php', [
        'user' => $user,
        'subscribers' => $subscribers,
        'publications' => $publications,
        'posts' => $posts,
        'post_likes' => $post_likes,
        'post_hashtags' => $post_hashtags,
        'is_subscribe' => $is_subscribe,
        'reposts' => $reposts,
        'repost_info' => $repost_info
    ]);

    $layout_content = include_template('layout.php', [
        'content'   => $page_content,
        'title'     => $page_titles['profile']
    ]);

    print($layout_content);

