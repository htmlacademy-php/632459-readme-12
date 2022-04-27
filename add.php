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

    $sql_types = 'SELECT id, type, name FROM content_types ORDER BY priority';
    $result = form_sql_request($con, $sql_types, []);
    $types = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $type = filter_input(INPUT_GET, 'type');

    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $inputArray = array_merge($_GET, $_POST, $_FILES);
        var_dump($errors = validateForm($inputArray, $validate_rules, $con));
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

    return $errors;
