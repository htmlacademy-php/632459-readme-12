<?php
    $is_auth = rand(0, 1);

    $user_name = 'Алина Глинская';

    $page_titles = [
        'index' => 'readme: популярное',
        'post' => 'readme: страница поста'
    ];

    $validate_rules = [
        'type' => 'required|in:quote,text,photo,video,link',
        'title' => 'required',
        'image' => 'required_if:photo|uploaded_file',
        'img_url' => 'url',
        'link' => 'required_if:link|url',
        'text' => 'required_if:text',
        'video' => 'required_if:video|video_url',
        'cite-text' => 'required_if:quote|min:1|max:70',
        'quote-author' => 'required_if:quote',
        'tags' => 'tags',
        'email-reg' => 'required|email|unique:users, email',
        'email-auth' => 'required|email|exists:users,email',
        'date' => 'date'
    ];

    $input_names = [
        'img_url' => 'Ссылка из интернета',
        'title' => 'Заголовок',
        'video' => 'Ссылка на видео',
        'tags' => 'Тэги',
        'text' => 'Текст',
        'link' => 'Ссылка',
        'cite-text' => 'Текст цитаты',
        'quote-author' => 'Автор цитаты'
    ];

    return [$is_auth, $user_name, $page_titles, $validate_rules, $input_names];
