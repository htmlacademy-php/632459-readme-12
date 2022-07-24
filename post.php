<?php
    require_once 'init.php';
    require_once 'helpers.php';
    require_once 'functions.php';
    require_once 'data.php';

    if (!$_SESSION['user']) {
        header("Location: /");
        exit();
    }

    $con = require('init.php');
    [$is_auth, $user_name, $page_titles] = require('data.php');

    if (!$con) {
        $error = mysqli_connect_error();
        print("Ошибка подключения: " . $error);
        die();
    }

    if (!$_SESSION['user']) {
        header("Location: /");
    }

    $search_query = filter_input(INPUT_GET, 'search');
    if (!empty($search_query)) {
        header("Location: /search.php?search=$search_query");
    }

    /* Данные о посте и пользователе */
    $post_id = filter_input(INPUT_GET, 'post', FILTER_VALIDATE_INT);

    $sql_posts = 'SELECT posts.*, type, login, avatar_path, dt_reg FROM posts '
    . 'JOIN content_types c ON content_type = c.id '
    . 'JOIN users u ON user_id = u.id '
    . 'WHERE posts.id = ?';

    $result = form_sql_request($con, $sql_posts, [$post_id]);

    $post = mysqli_fetch_array($result);

    $sql_reposts = 'SELECT COUNT(*) as COUNT FROM posts WHERE parent_id = ?';
    $result = form_sql_request($con, $sql_reposts, [$post_id]);
    $reposts = mysqli_fetch_array($result);

    /* Подсчет подписчиков пользователя */
    $sql_subscribers = 'SELECT COUNT(follower_id) AS total FROM subscriptions '
    . 'JOIN posts p ON p.user_id = subscribe_id '
    . 'WHERE p.id = ?';

    $result = form_sql_request($con, $sql_subscribers, [$post_id]);

    $subscribers = mysqli_fetch_array($result);

    /* Подсчет публикаций пользователя */

    $sql_publications = 'SELECT COUNT(id) AS total FROM posts '
    . 'WHERE user_id = (SELECT user_id FROM posts '
    . 'WHERE id = ?)';

    $result = form_sql_request($con, $sql_publications, [$post_id]);

    $publications = mysqli_fetch_array($result);

    /* Подсчет лайков поста */

    $sql_likes = 'SELECT COUNT(id) AS total FROM likes '
    . 'WHERE like_post_id = ?';

    $result = form_sql_request($con, $sql_likes, [$post_id]);

    $likes = mysqli_fetch_array($result);

    /* Хэштеги к посту */

    $sql_hashtags = 'SELECT hashtag_name FROM posts p '
    . 'JOIN post_tags pt ON p.id=pt.post_id '
    . 'JOIN hashtags h ON pt.hashtag_id=h.id '
    . 'WHERE p.id = ?';

    $result = form_sql_request($con, $sql_hashtags, [$post_id]);

    $hashtags = mysqli_fetch_all($result, MYSQLI_ASSOC);

    /* Данные о комментариях и авторах */

    $sql_comments = 'SELECT date_add, text, login, avatar_path, user_id FROM comments c '
    . 'JOIN users u ON u.id=c.user_id '
    . 'WHERE c.post_id = ?';

    $result = form_sql_request($con, $sql_comments, [$post_id]);

    $comments = mysqli_fetch_all($result, MYSQLI_ASSOC);

    /* Количество комментариев */

    $sql_comments_amount = 'SELECT COUNT(id) AS total FROM comments '
    . 'WHERE post_id = ?';

    $result = form_sql_request($con, $sql_comments_amount, [$post_id]);

    $comments_amount = mysqli_fetch_array($result);

    /* Подключение шаблонов */

    switch ($post['type']) {
        case 'link':
            $post_main = include_template('post-link.php', [
                'url' => $post['link'],
                'title' => $post['title']
            ]);
            break;
        case 'photo':
            $post_main = include_template('post-photo.php', [
                'img_url' => $post['img']
            ]);
            break;
        case 'quote':
            $post_main = include_template('post-quote.php', [
                'text' => $post['text'],
                'author' => $post['cite_author']
            ]);
            break;
        case 'text':
            $post_main = include_template('post-text.php', [
                'text' => $post['text']
            ]);
            break;
        case 'video':
            $post_main = include_template('post-video.php', [
                'youtube_url' => $post['video']
            ]);
            break;
        default:
            $post_main = '';
    }

    if ($post_id && !$post) {
        header('HTTP/1.0 404 not found');
    }

    if ($post_id && $post) {
        $page_content = include_template('post-details.php',[
            'post' => $post,
            'post_main' => $post_main,
            'subscribers' => $subscribers,
            'publications' => $publications,
            'likes' => $likes,
            'hashtags' => $hashtags,
            'comments' => $comments,
            'comments_amount' => $comments_amount,
            'reposts' => $reposts
        ]);

        $layout_content = include_template('layout.php', [
            'content' => $page_content,
            'title' => $page_titles['post'],
            'user_name' => $user_name,
            'is_auth' => $is_auth
        ]);

        print($layout_content);
    }



