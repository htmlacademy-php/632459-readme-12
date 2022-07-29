<?php
    require_once 'init.php';
    require_once 'helpers.php';
    require_once 'functions.php';

    define('TYPE_QUOTE', 'quote');
    define('TYPE_TEXT', 'text');
    define('TYPE_PHOTO', 'photo');
    define('TYPE_VIDEO', 'video');
    define('TYPE_LINK', 'link');

    if (!$_SESSION['user']) {
        header("Location: /");
        exit();
    }

    [$is_auth, $user_name, $page_titles, $validate_rules, $input_names] = require('data.php');
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

    $type = filter_input(INPUT_GET, 'type');

    $type_id = getTypeId($types, $type);

    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $inputArray = array_merge($_GET, $_POST, $_FILES);
        $errors = validateForm($inputArray, $validate_rules, $con);

        if(empty($errors)) {

            switch($type) {
                case TYPE_QUOTE:
                    $sql_post = 'INSERT INTO posts (date_add, user_id, show_count, content_type, title, text, cite_author) VALUES (NOW(), ?, 0, ?, ?, ?, ?)';
                    $params =  [$_SESSION['user']['id'], $type_id, $inputArray['title'], $inputArray['cite-text'], $inputArray['quote-author']];
                    $result = form_sql_request($con, $sql_post, $params, false);

                    if ($result) {
                        $new_post_id = mysqli_insert_id($con);
                        getHashtags($inputArray, $con, $new_post_id);
                        header("Location: post.php?post=" . $new_post_id);
                    }

                    break;

                case TYPE_TEXT:
                    $sql_post = 'INSERT INTO posts (date_add, user_id, show_count, content_type, title, text) VALUES (NOW(), ?, 0, ?, ?, ?)';
                    $params =  [$_SESSION['user']['id'], $type_id, $inputArray['title'], $inputArray['text']];

                    $result = form_sql_request($con, $sql_post, $params, false);

                    if ($result) {
                        $new_post_id = mysqli_insert_id($con);
                        getHashtags($inputArray, $con, $new_post_id);
                        header("Location: post.php?post=" . $new_post_id);
                    }

                    break;

                case TYPE_PHOTO:
                    $file_field = 'image';

                    if (file_exists($inputArray[$file_field]['tmp_name']) || is_uploaded_file($inputArray[$file_field]['tmp_name'])) {
                        $img_path = getUploadedFile($inputArray, $file_field);
                    } else {
                        $img_path = getUrlContent($inputArray);
                    }

                    $sql_post = 'INSERT INTO posts (date_add, user_id, show_count, content_type, title, img) VALUES (NOW(), ?, 0, ?, ?, ?)';
                    $params =  [$_SESSION['user']['id'], $type_id, $inputArray['title'], $img_path];

                    $result = form_sql_request($con, $sql_post, $params, false);

                    if ($result) {
                        $new_post_id = mysqli_insert_id($con);
                        getHashtags($inputArray, $con, $new_post_id);
                        header("Location: post.php?post=" . $new_post_id);
                    }

                    break;

                case TYPE_VIDEO:
                    $sql_post = 'INSERT INTO posts (date_add, user_id, show_count, content_type, title, video) VALUES (NOW(), ?, 0, ?, ?, ?)';
                    $params =  [$_SESSION['user']['id'], $type_id, $inputArray['title'], $inputArray['video']];

                    $result = form_sql_request($con, $sql_post, $params, false);

                    if ($result) {
                        $new_post_id = mysqli_insert_id($con);
                        getHashtags($inputArray, $con, $new_post_id);
                        header("Location: post.php?post=" . $new_post_id);
                    }

                    break;

                case TYPE_LINK:
                    $sql_post = 'INSERT INTO posts (date_add, user_id, show_count, content_type, title, link) VALUES (NOW(), ?, 0, ?, ?, ?)';
                    $params =  [$_SESSION['user']['id'], $type_id, $inputArray['title'], $inputArray['link']];

                    $result = form_sql_request($con, $sql_post, $params, false);

                    if ($result) {
                        $new_post_id = mysqli_insert_id($con);
                        getHashtags($inputArray, $con, $new_post_id);
                        header("Location: post.php?post=" . $new_post_id);
                    }

                    break;
            }
        }
    }

    $title_input = include_template('title-input.php', [
        'errors' => $errors,
        'input_names' => $input_names
    ]);

    $tags_input = include_template('tags-input.php', [
        'errors' => $errors,
        'input_names' => $input_names
    ]);

    $page_content = include_template('add.php', [
        'types' => $types,
        'title_input' => $title_input,
        'tags_input' => $tags_input,
        'errors' => $errors,
        'input_names' => $input_names
    ]);

    print($page_content);
