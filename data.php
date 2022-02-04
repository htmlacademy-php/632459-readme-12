<?php
    $is_auth = rand(0, 1);

    $user_name = 'Алина Глинская';

    $page_titles = [
        'index' => 'readme: популярное'
    ];

    $popular_posts = [
        [
            'title' => 'Цитата',
            'type' => 'post-quote',
            'content' => 'Мы в жизни любим только раз, а после ищем лишь похожих',
            'user_name' => 'Лариса',
            'avatar_url' => 'userpic-larisa-small.jpg'
        ],
        [
            'title' => 'Игра престолов',
            'type' => 'post-text',
            'content' => 'Не могу дождаться начала финального сезона своего любимого сериала!',
            'user_name' => 'Владик',
            'avatar_url' => 'userpic.jpg'
        ],
        [
            'title' => 'Наконец, обработал фотки!',
            'type' => 'post-photo',
            'content' => 'rock-medium.jpg',
            'user_name' => 'Виктор',
            'avatar_url' => 'userpic-mark.jpg'
        ],
        [
            'title' => 'Моя мечта',
            'type' => 'post-photo',
            'content' => 'coast-medium.jpg',
            'user_name' => 'Лариса',
            'avatar_url' => 'userpic-larisa-small.jpg'
        ],
        [
            'title' => 'Лучшие курсы',
            'type' => 'post-link',
            'content' => 'www.htmlacademy.ru',
            'user_name' => 'Владик',
            'avatar_url' => 'userpic.jpg'
        ]
    ];
?>
