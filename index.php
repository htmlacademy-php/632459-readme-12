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

    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $inputArray = $_POST;
        $errors = validateForm($inputArray, $validate_rules, $con);

        if (empty($errors)) {
            $sql_user = 'SELECT id, login, avatar_path FROM users WHERE email = ?';
            $result = formSqlRequest($con, $sql_user, [$inputArray['authorize-login']]);
            $user = mysqli_fetch_array($result, MYSQLI_ASSOC);

            $_SESSION['user'] = $user;
            header("Location: /feed.php?user=" . $user['id']);
        }
    }

    $page_content = include_template('anonim-layout.php', [
        'errors' => $errors
    ]);

    print($page_content);




