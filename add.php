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

    $sql_types = 'SELECT id, type, name FROM content_types ORDER BY priority';
    $result = form_sql_request($con, $sql_types, []);
    $types = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $tab = filter_input(INPUT_GET, 'tab');

    $title_input = include_template('title-input.php', []);
    $tags_input = include_template('tags-input.php', []);

    $page_content = include_template('add.php', [
        'types' => $types,
        'title_input' => $title_input,
        'tags_input' => $tags_input
    ]);

    print($page_content);
