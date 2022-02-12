<?php
    require_once 'init.php';
    require_once 'helpers.php';
    require_once 'functions.php';
    require_once 'data.php';

    $page_content = include_template('main.php', [
        'popular_posts' => $popular_posts
    ]);

    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'title' => $page_titles['index'],
        'user_name' => $user_name,
        'is_auth' => $is_auth
    ]);

    print($layout_content);
