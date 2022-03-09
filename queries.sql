INSERT INTO content_types(type, class) VALUES
  ("quote", "post-quote"),
  ("text", "post-text"),
  ("photo", "post-photo"),
  ("video", "post-video"),
  ("link", "post-link");

INSERT INTO users(dt_reg, email, login, password, avatar_path) VALUES
  ("2021-03-01 13:10:10", "dmitriy1983@mail.ru", "Dima", "qwerty", "userpic.jpg"),
  ("2020-11-23 23:50:00", "ivan_smirnov@gmail.com", "Werwolf", "123456", "userpic.jpg");

INSERT INTO posts
  SET date_add = "2020-05-12 12:00:05", title = "Цитата", text = "Мы в жизни любим только раз, а после ищем лишь похожих", cite_author = "Неизвестный Автор", show_count = 111, user_id = "1", content_type = "1";

INSERT INTO posts
  SET date_add = "2019-12-12 10:05:16", title = "Игра Престолов", text = "Не могу дождаться начала финального сезона своего любимого сериала!", show_count = 1756, user_id = "2", content_type = "2";

INSERT INTO posts
  SET date_add = "2021-11-10 23:45:00", title = "Наконец, обработал фотки!", img = "rock-medium.jpg", show_count = 98, user_id = "1", content_type = "3";

INSERT INTO posts
  SET date_add = "2020-03-08 11:11:00", title = "Моя мечта", img = "coast-medium.jpg", show_count = 229, user_id = "2", content_type = "3";

INSERT INTO posts
  SET date_add = "2021-05-17 17:20:20", title = "Лучшие курсы", text = "Лучшие курсы", link = "www.htmlacademy.ru", show_count = 500, user_id = "1", content_type = "5";

INSERT INTO comments(date_add, text, user_id, post_id) VALUES
  ("2022-02-22 14:14:36", "Сколько же еще ждать «Ветра Зимы»...", 1, 2),
  ("2022-02-15 10:26:15", "Академия ван лав", 2, 5);


-- Сортирует посты по популярности, с указанием автора и типа контента

SELECT posts.*, login, type FROM posts
JOIN users u ON user_id = u.id
JOIN content_types c ON content_type = c.id
ORDER BY show_count DESC;

-- Список постов для конкретного пользователя

SELECT posts.*, login FROM posts
JOIN users u ON user_id = u.id
WHERE login = 'Dima';

-- Список комментариев к посту с логином пользователя

SELECT comments.*, login FROM comments
JOIN users u ON user_id = u.id
WHERE post_id = 2;

-- Добавление лайка к посту

INSERT INTO likes(like_user_id, like_post_id) VALUES (1, 2);

-- Подписка на пользователя

INSERT INTO subscriptions(subscribe_id, follower_id) VALUES (1, 2);

