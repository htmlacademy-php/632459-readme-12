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

    $post_id = filter_input(INPUT_GET, 'post');

    $sql_post = 'SELECT * FROM posts WHERE id = ?';
    $result = formSqlRequest($con, $sql_post, [$post_id]);

    if (mysqli_num_rows($result) > 0) {
        $post = mysqli_fetch_array($result);
        $sql_repost = 'INSERT INTO posts SET date_add = NOW(), title = ?, text = ?, cite_author = ?, img = ?, video = ?, link = ?, show_count = '
        . '?, content_type = ?, user_id = ?, repost = true, original_author = ?, parent_id = ?';
        formSqlRequest($con, $sql_repost, [
            $post['title'],
            $post['text'],
            $post['cite_author'],
            $post['img'],
            $post['video'],
            $post['link'],
            $post['show_count'],
            $post['content_type'],
            $_SESSION['user']['id'],
            $post['user_id'],
            $post['id']],
            false);
    }

    header("Location: /profile.php?user=" . $_SESSION['user']['id'] . '&tab=posts');



