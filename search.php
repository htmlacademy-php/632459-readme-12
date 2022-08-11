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

    $search_text = trim($search_query);

    if (empty($search_text)) {
        $page_content = include_template('no-results.php', [
            'search_text' => $search_text,
        ]);

        $layout_content = include_template('layout.php', [
            'content'   => $page_content,
            'title'     => $page_titles['search_results']
        ]);

        print($layout_content);
        return;
    }

    $sql_posts = 'SELECT posts.*, login, avatar_path, class, type, '
    . '(SELECT COUNT(comment.id) FROM comments AS comment WHERE comment.post_id = posts.id) AS comments_count, '
    . '(SELECT COUNT(liked.id) FROM likes AS liked WHERE liked.like_post_id = posts.id) AS likes_count '
        . 'FROM posts JOIN users u ON user_id = u.id '
        . 'JOIN content_types ct ON content_type = ct.id '
        . 'JOIN comments com ON com.post_id = (posts.id OR NULL) '
        . 'WHERE MATCH(posts.title,posts.text) AGAINST(?) GROUP BY posts.id';

        $result = form_sql_request($con, $sql_posts, [$search_text]);

    if (substr($search_text, 0, 1) === "#") {
        $hashtag = substr($search_text, 1, strlen($search_text) - 1);

        $sql_posts = 'SELECT posts.*, login, avatar_path, class, type, '
        . '(SELECT COUNT(comment.id) FROM comments AS comment WHERE comment.post_id = posts.id) AS comments_count, '
        . '(SELECT COUNT(liked.id) FROM likes AS liked WHERE liked.like_post_id = posts.id) AS likes_count '
            . 'FROM posts JOIN users u ON user_id = u.id '
            . 'JOIN content_types ct ON content_type = ct.id '
            . 'JOIN comments com ON com.post_id = (posts.id OR NULL) '
            . 'JOIN post_tags pt ON posts.id=pt.post_id '
            . 'JOIN hashtags h ON pt.hashtag_id=h.id '
            . 'WHERE hashtag_name = ? GROUP BY posts.id ORDER BY date_add DESC';

        $result = form_sql_request($con, $sql_posts, [$hashtag]);
    }

    $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $page_content = include_template('search-results.php', [
        'search_text' => $search_text,
        'posts' => $posts
    ]);

    if (empty($posts)) {
        $page_content = include_template('no-results.php', [
            'search_text' => $search_text,
        ]);
    }

    $layout_content = include_template('layout.php', [
        'content'   => $page_content,
        'title'     => $page_titles['search_results']
    ]);

    print($layout_content);
