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
INSERT INTO likes(like_user_id, like_post_id) VALUES
(1, 3),
(1, 4),
(1, 5),
(2, 1),
(2, 3),
(2, 4),
(3, 4),
(3, 5),
(5, 1),
(5, 2),
(7, 1),
(7, 2),
(7, 5),
(8, 1),
(9, 1),
(9, 3),
(9, 5);

-- Подписка на пользователя

INSERT INTO subscriptions(subscribe_id, follower_id) VALUES (1, 2);
INSERT INTO subscriptions(subscribe_id, follower_id) VALUES
  (3, 2),
  (3, 4),
  (9, 2),
  (9, 1),
  (1, 5),
  (1, 6),
  (7, 8),
  (4, 2),
  (9, 2),
  (7, 2);

-- Добавление значений в content_types

UPDATE content_types
SET name = "Цитата", priority = 4 WHERE id = 1;

UPDATE content_types
SET name = "Текст", priority = 3 WHERE id = 2;

UPDATE content_types
SET name = "Фото", priority = 1 WHERE id = 3;

UPDATE content_types
SET name = "Видео", priority = 2 WHERE id = 4;

UPDATE content_types
SET name = "Ссылка", priority = 5 WHERE id = 5;


-- Добавление пользователей

INSERT INTO users(dt_reg, email, login, password, avatar_path) VALUES
  ("2021-03-11 16:10:00", "lara@mail.ru", "Лариса", "123", "userpic-larisa-small.jpg"),
  ("2020-11-01 13:15:10", "vlad777@mail.ru", "Владик", "456", "userpic.jpg"),
  ("2021-11-24 23:55:00", "v_ivanov@gmail.com", "Виктор", "11111", "userpic-mark.jpg");

INSERT INTO users(dt_reg, email, login, password, avatar_path) VALUES
  ("2018-09-09 15:00:01", "markopolo@mail.ru", "Марк Смолов", "f00fcf", "userpic-mark.jpg"),
  ("2017-03-09 13:07:07", "elvira1987@mail.ru", "Эльвира Хайпулинова", "135el", "userpic-elvira.jpg"),
  ("2022-01-14 22:15:00", "firsovatanyua@gmail.com", "Таня Фирсова", "awesome", "userpic-tanya.jpg"),
  ("2021-01-29 23:23:20", "petr-demin@mail.ru", "Петр Демин", "wasd!;", "userpic-petro.jpg");

UPDATE users
SET login = "Лариса Роговая" WHERE id = 3;

-- Привязка пользователей к существующим постам

UPDATE posts
SET user_id = 3 WHERE id = 1;

UPDATE posts
SET user_id = 4 WHERE id = 2;

UPDATE posts
SET user_id = 9 WHERE id = 3;

UPDATE posts
SET user_id = 7 WHERE id = 4;

UPDATE posts
SET user_id = 3 WHERE id = 5;

-- Добавление хэштегов

INSERT INTO hashtags(hashtag_name) VALUES
  ('шикарный вид'),
  ('globe'),
  ('landscape'),
  ('nature'),
  ('canon'),
  ('photooftheday'),
  ('курсы'),
  ('цитата'),
  ('любовь'),
  ('сериалы'),
  ('gameofthrones'),
  ('htmlacademy'),
  ('html'),
  ('php'),
  ('postoftheday');

-- Привязка хэштегов к постам

INSERT INTO post_tags(post_id, hashtag_id) VALUES
  (1, 8),
  (1, 9),
  (1, 15),
  (2, 10),
  (2, 11),
  (3, 1),
  (3, 2),
  (3, 3),
  (3, 4),
  (3, 5),
  (3, 6),
  (4, 1),
  (4, 2),
  (4, 3),
  (4, 4),
  (4, 5),
  (5, 7),
  (5, 12),
  (5, 13),
  (5, 14),
  (5, 15);

-- Добавление комментариев

INSERT INTO comments(date_add, text, user_id, post_id) VALUES
  ("2022-04-01 13:00:05", "Красота!!!", 3, 3),
  ("2022-03-03 14:15:07", "Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал считается самым глубоким озером в мире. Он окружен сетью пешеходных маршрутов, называемых Большой байкальской тропой. Деревня Листвянка, расположенная на западном берегу озера, – популярная отправная точка для летних экскурсий. Зимой здесь можно кататься на коньках и собачьих упряжках.", 3, 3),
  ("2021-11-11 12:42:00", "Жизненно...", 8, 1),
  ("2021-12-30 23:00:10", "«Ходячие Мертвецы» тоже крутой сериал", 9, 2),
  ("2022-02-25 22:12:12", "Думаю, каждый хоть раз мечтал о домике на берегу моря :)", 4, 4),
  ("2022-02-14 21:00:45", "Мечты сбываются, стоит только расхотеть", 9, 4),
  ("2022-03-09 16:33:00", "Учусь в Академии с 2018 года", 6, 5),
  ("2022-03-09 15:12:08", "Интересно, кто автор", 7, 1),
  ("2022-01-15 16:12:00", "Купила себе всю серию книг «Песнь льда и пламени»", 7, 2);

