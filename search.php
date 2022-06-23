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

    $search_query = filter_input(INPUT_GET, 'search');

    $search_text = trim($search_query);

    if (!empty($search_text)) {
        if (substr($search_text, 0, 1) === "#") {
            $hashtag = substr($search_text, 1, strlen($search_text) - 1);

            $sql_posts = 'SELECT posts.*, login, avatar_path, class, type FROM posts '
            . 'JOIN users u ON user_id = u.id '
            . 'JOIN content_types ct ON content_type = ct.id '
            . 'JOIN post_tags pt ON posts.id=pt.post_id '
            . 'JOIN hashtags h ON pt.hashtag_id=h.id '
            . 'WHERE hashtag_name = ?';

            $result = form_sql_request($con, $sql_posts, [$hashtag]);
            $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $sql_posts = 'SELECT posts.*, login, avatar_path, class, type FROM posts '
            . 'JOIN users u ON user_id = u.id '
            . 'JOIN content_types ct ON content_type = ct.id '
            . 'WHERE MATCH(title,text) AGAINST(?)';

            $result = form_sql_request($con, $sql_posts, [$search_text]);
            $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        $post_comments = [];
        $post_likes = [];

        foreach ($posts as $post) {
            $sql_comments = 'SELECT COUNT(id) AS total FROM comments '
            . 'WHERE post_id = '. $post['id'];
            $result = form_sql_request($con, $sql_comments, []);
            $comments = mysqli_fetch_all($result, MYSQLI_ASSOC);
            array_push($post_comments, $comments[0]['total']);

            $sql_likes = 'SELECT COUNT(id) AS total FROM likes '
            . 'WHERE like_post_id = '. $post['id'];
            $result = form_sql_request($con, $sql_likes, []);
            $likes = mysqli_fetch_all($result, MYSQLI_ASSOC);
            array_push($post_likes, $likes[0]['total']);
        }
    }

    if (empty($posts)) {
        $page_content = include_template('no-results.php', [
            'search_text' => $search_text,
        ]);
    } else {
        $page_content = include_template('search-results.php', [
            'search_text' => $search_text,
            'posts' => $posts,
            'post_comments' => $post_comments,
            'post_likes' => $post_likes
        ]);
    }

    $layout_content = include_template('layout.php', [
        'content'   => $page_content,
        'title'     => $page_titles['search_results']
    ]);

    print($layout_content);
