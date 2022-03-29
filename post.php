<?php
    require_once 'init.php';
    require_once 'helpers.php';
    require_once 'functions.php';
    require_once 'data.php';

    $page_content = include_template('post-details.php', [
        'post' => $post
    ]);

    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'title' => $page_titles['post'],
        'user_name' => $user_name,
        'is_auth' => $is_auth,
        'class' => $main_classes['post']
    ]);

    print($layout_content);
