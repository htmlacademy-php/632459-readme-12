<?php
    require_once 'init.php';
    require_once 'helpers.php';
    require_once 'functions.php';

    [$is_auth, $user_name, $page_titles, $validate_rules, $input_names] = require('data.php');

    if (!$con) {
        $error = mysqli_connect_error();
        print("Ошибка подключения: " . $error);
        die();
    }

    $search_query = filter_input(INPUT_GET, 'search');
    if (!empty($search_query)) {
        header("Location: /search.php?search=$search_query");
    }

    if (!isset($_SESSION['user'])) {
        header('Location: /');
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

    if (mysqli_num_rows($result) > 0) {
        $is_subscribe = true;
    }

    $sql_posts = 'SELECT posts.*, type, class, original_author, login, avatar_path, '
    . '(SELECT COUNT(liked.id) FROM likes AS liked WHERE liked.post_id = posts.id) AS likes_count '
    . 'FROM posts '
    . 'LEFT JOIN users u ON u.id = original_author '
    . 'JOIN content_types c ON content_type = c.id '
    . 'WHERE user_id = ? GROUP BY posts.id '
    . 'ORDER BY date_add DESC';

    $result = form_sql_request($con, $sql_posts, [$user_id]);
    $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $tags_to_posts = [];

    if (!empty($posts)) {
        $post_ids = array_reduce($posts, function($carry, $post) {
            $carry[] = $post['id'];
            return $carry;
        }, []);

        $post_ids_res = implode(",", $post_ids);

        $sql_hashtags = 'SELECT post_id, h.name FROM post_tags
            JOIN hashtags h ON h.id = post_tags.hashtag_id
            WHERE post_id IN (' . $post_ids_res . ') GROUP BY post_id, h.name';

        $result = form_sql_request($con, $sql_hashtags, []);
        $hashtags = mysqli_fetch_all($result, MYSQLI_ASSOC);

        foreach ($hashtags as $hashtag) {
            $tags_to_posts[$hashtag['post_id']][] = $hashtag['name'];
        }

        foreach ($posts as $i => $post) {
            $post['tags'] = $tags_to_posts[$post['id']] ?? [];
            $posts[$i] = $post;
        }
    }

    $sql_reposts = 'SELECT id, (SELECT COUNT(*) FROM posts p WHERE p.parent_id = posts.id GROUP BY p.parent_id) AS repost_count FROM posts';
    $result = form_sql_request($con, $sql_reposts, []);
    $reposts = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $sql_profile_likes = 'SELECT l.*, img, type, name, u.id, login, avatar_path FROM likes l '
    . 'JOIN posts p ON p.id = l.post_id '
    . 'JOIN content_types c ON content_type = c.id '
    . 'JOIN users u ON u.id = l.user_id '
    . 'WHERE p.user_id = ? ORDER BY l.date DESC';

    $result = form_sql_request($con, $sql_profile_likes, [$user['id']]);
    $profile_likes = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $sql_profile_subscribes = 'SELECT s.subscribe_id, u.id, login, avatar_path, dt_reg, '
        . '(SELECT COUNT(follower.follower_id) FROM subscriptions AS follower WHERE follower.subscribe_id = s.subscribe_id) AS subscription_count, '
        . '(SELECT COUNT(post.user_id) FROM posts AS post WHERE post.user_id = s.subscribe_id) AS posts_count, '
        . '(SELECT follower.follower_id from subscriptions as follower where follower.follower_id = ? and subscribe_id = s.subscribe_id) as is_sub '
        . 'FROM subscriptions s '
        . 'JOIN users u ON u.id = s.subscribe_id '
        . 'JOIN posts p ON p.user_id = u.id '
        . 'WHERE follower_id = ? GROUP BY s.subscribe_id';

    $result = form_sql_request($con, $sql_profile_subscribes, [$_SESSION['user']['id'], $user_id]);
    $profile_subs = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $page_content = include_template('profile.php', [
        'user' => $user,
        'subscribers' => $subscribers,
        'publications' => $publications,
        'posts' => $posts,
        'is_subscribe' => $is_subscribe,
        'reposts' => $reposts,
        'profile_likes' => $profile_likes,
        'profile_subs' => $profile_subs,
        'tags_to_posts' => $tags_to_posts
    ]);

    $layout_content = include_template('layout-profile.php', [
        'content'   => $page_content,
        'title'     => $page_titles['profile'],
        'user_id' => $user_id
    ]);

    print($layout_content);

