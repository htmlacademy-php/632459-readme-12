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

    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $inputArray = array_merge($_GET, $_POST, $_FILES);
        $errors = validateForm($inputArray, $validate_rules, $con);
        $file_field = 'userpic-file';

        if (empty($errors)) {
            if (file_exists($inputArray[$file_field]['tmp_name']) || is_uploaded_file($inputArray[$file_field]['tmp_name'])) {
                $img_path = getUploadedFile($inputArray, $file_field);
            }

            $passwordHash = password_hash($inputArray['password'], PASSWORD_DEFAULT);
            $sql = 'INSERT INTO users (dt_reg, email, login, password, avatar_path) VALUES (NOW(), ?, ?, ?, ?)';
            $params = [$inputArray['email-reg'], $inputArray['login'], $passwordHash, $img_path];


            $result = formSqlRequest($con, $sql, $params, false);

            if ($result) {
                header("Location: /");
            }
        }
    }

    $page_content = include_template('reg.php', [
        'errors' => $errors,
        'input_names' => $input_names
    ]);

    $layout_content = include_template('layout.php', [
        'content'   => $page_content,
        'title'     => $page_titles['reg'],
        'user_name' => $user_name,
        'is_auth'   => 0

    ]);

    print($layout_content);
