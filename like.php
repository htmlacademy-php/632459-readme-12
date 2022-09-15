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

    $sql_post_id = 'SELECT id FROM posts WHERE id = ?';
    $result = formSqlRequest($con, $sql_post_id, [$post_id]);

    if (mysqli_num_rows($result) > 0) {
        $sql_like = 'INSERT INTO likes (user_id, post_id, date) VALUES (?, ?, NOW())';
        formSqlRequest($con, $sql_like, [$_SESSION['user']['id'], $post_id], false);
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);



