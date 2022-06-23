<?php
    $is_auth = rand(0, 1);

    $user_name = 'Алина Глинская';

    $page_titles = [
        'index' => 'readme: популярное',
        'post' => 'readme: страница поста',
        'reg' => 'readme: регистрация',
        'feed' => 'readme: моя лента',
        'search_results'=> 'readme: результаты поиска'
    ];

    $validate_rules = [
        'type' => 'required|in:quote,text,photo,video,link',
        'title' => 'required',
        'image' => 'required_unless:img_url,image|uploaded_file',
        'img_url' => 'required_unless:image,img_url|url|url_content',
        'link' => 'required_if_value:type,link|url',
        'text' => 'required_if_value:type,text',
        'video' => 'required_if_value:type,video|url|video_url',
        'cite-text' => 'required_if_value:type,quote|min:1|max:70|string',
        'quote-author' => 'required_if_value:type,quote|string',
        'tags' => 'tags',
        'email-reg' => 'required|email|unique:users, email',
        'email-auth' => 'required|email|exists:users,email',
        'date' => 'date',
        'login' => 'required|string',
        'password' => 'required|password:password,password-repeat',
        'password-repeat'=> 'required|password:password,password-repeat',
        'userpic-file' => 'uploaded_file',
        'authorize-login' => 'required|email|exists:users,email',
        'authorize-password' => 'required|verify:authorize-login,users,email,password'
    ];

    $input_names = [
        'img_url' => 'Ссылка из интернета',
        'title' => 'Заголовок',
        'video' => 'Ссылка на видео',
        'tags' => 'Тэги',
        'text' => 'Текст',
        'link' => 'Ссылка',
        'cite-text' => 'Текст цитаты',
        'quote-author' => 'Автор цитаты',
        'image' => ' Файл',
        'email-reg' => 'Электронная почта',
        'login' => 'Логин',
        'password'=> 'Пароль',
        'password-repeat' => 'Повтор пароля',
        'userpic-file' => 'Фото'
    ];

    return [$is_auth, $user_name, $page_titles, $validate_rules, $input_names];
