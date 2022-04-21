<?php
    $is_auth = rand(0, 1);

    $user_name = 'Алина Глинская';

    $page_titles = [
        'index' => 'readme: популярное',
        'post' => 'readme: страница поста'
    ];

    $validate_rules = [
        'current-tab' => 'required|in:quote,text,photo,video,link',
        'title' => 'required',
        'image' => 'required_if_value:type,photo|uploaded_file|image',
        'link' => 'required_if_value:type,link|url',
        'text' => 'required_if_value:type,text,quote|string|min:1|max:70',
        'video' => 'required_if_value:type,video|video_url',
        'tags' => 'tags',
        'email_reg' => 'required|email|unique:users, email',
        'email_auth' => 'required|email|exists:users,email',
        'date' => 'date'
    ];

    return [$is_auth, $user_name, $page_titles, $validate_rules];
