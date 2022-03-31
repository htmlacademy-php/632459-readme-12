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

    /* Данные о посте и пользователе */
    $post_id = filter_input(INPUT_GET, 'post');

    $sql_posts = 'SELECT posts.*, type, login, avatar_path FROM posts '
    . 'JOIN content_types c ON content_type = c.id '
    . 'JOIN users u ON user_id = u.id '
    . 'WHERE posts.id = ?';

    $stmt = db_get_prepare_stmt($con, $sql_posts, [$post_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        $error = mysqli_error($con);
        print("Ошибка подключения: " . $error);
        die();
    }

    $post = mysqli_fetch_array($result);

    /* Подсчет подписчиков пользователя */
    $sql_subscribers = 'SELECT COUNT(follower_id) AS total FROM subscriptions '
    . 'JOIN posts p ON p.user_id = subscribe_id '
    . 'WHERE p.id = ?';

    $stmt = db_get_prepare_stmt($con, $sql_subscribers, [$post_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        $error = mysqli_error($con);
        print("Ошибка подключения: " . $error);
        die();
    }

    $subscribers = mysqli_fetch_array($result);

    /* Подсчет публикаций пользователя */

    $sql_publications = 'SELECT COUNT(id) AS total FROM posts '
    . 'WHERE user_id = (SELECT user_id FROM posts '
    . 'WHERE id = ?)';

    $stmt = db_get_prepare_stmt($con, $sql_publications, [$post_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        $error = mysqli_error($con);
        print("Ошибка подключения: " . $error);
        die();
    }

    $publications = mysqli_fetch_array($result);

    $post_link = include_template('post-link.php', [
        'url' => $post['link'],
        'title' => $post['title']
    ]);

    $post_photo = include_template('post-photo.php', [
        'img_url' => $post['img']
    ]);

    $post_quote = include_template('post-quote.php', [
        'text' => $post['text'],
        'author' => $post['cite_author']
    ]);

    $post_text = include_template('post-text.php', [
        'text' => $post['text']
    ]);

    $post_video = include_template('post-video.php', [
        'youtube_url' => $post['video']
    ]);

    switch ($post['type']) {
        case 'link':
            $post_main = $post_link;
            break;
        case 'photo':
            $post_main = $post_photo;
            break;
        case 'quote':
            $post_main = $post_quote;
            break;
        case 'text':
            $post_main = $post_text;
            break;
        case 'video':
            $post_main = $post_video;
            break;
    }

    if ($post_id && !$post) {
        header('HTTP/1.0 404 not found');
    }

    if ($post_id) {
        $page_content = include_template('post-details.php',[
            'post' => $post,
            'post_main' => $post_main,
            'subscribers' => $subscribers,
            'publications' => $publications
        ]);

        $layout_content = include_template('layout.php', [
            'content' => $page_content,
            'title' => $page_titles['post'],
            'user_name' => $user_name,
            'is_auth' => $is_auth
        ]);

        print($layout_content);
    }



