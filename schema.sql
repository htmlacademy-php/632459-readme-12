CREATE DATABASE IF NOT EXISTS readme
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE readme;

CREATE TABLE IF NOT EXISTS users
(
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  dt_reg      DATETIME            NOT NULL,
  email       VARCHAR(255) UNIQUE NOT NULL,
  login       VARCHAR(255)        NOT NULL,
  password    VARCHAR(64)         NOT NULL,
  avatar_path VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS content_types
(
  id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  type  VARCHAR(255),
  class VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS hashtags
(
  id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name TEXT
);

CREATE TABLE IF NOT EXISTS posts
(
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  date_add        DATETIME NOT NULL,
  title           TEXT,
  text            TEXT,
  cite_author     TEXT,
  img             VARCHAR(255),
  video           VARCHAR(255),
  link            VARCHAR(255),
  show_count      INT UNSIGNED,
  user_id         INT UNSIGNED,
  content_type    INT UNSIGNED,
  repost          VARCHAR(255),
  original_author VARCHAR(255),
  parent_id       INT UNSIGNED,
  CONSTRAINT user_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT content_type_fk FOREIGN KEY (content_type) REFERENCES content_types (id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS comments
(
  id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  date_add DATETIME NOT NULL,
  text     TEXT,
  user_id  INT UNSIGNED,
  post_id  INT UNSIGNED,
  CONSTRAINT comment_author_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT commented_post_fk FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS messages
(
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  date_add    DATETIME NOT NULL,
  text        TEXT,
  sender_id   INT UNSIGNED,
  reciever_id INT UNSIGNED,
  new         INT UNSIGNED,
  CONSTRAINT sender_fk FOREIGN KEY (sender_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT reciever_fk FOREIGN KEY (reciever_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS subscriptions
(
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  subscribe_id INT UNSIGNED,
  follower_id  INT UNSIGNED,
  CONSTRAINT subscribe_fk FOREIGN KEY (subscribe_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT follower_fk FOREIGN KEY (follower_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS likes
(
  id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED,
  post_id INT UNSIGNED,
  date    DATETIME NOT NULL,
  CONSTRAINT like_user_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT like_post_fk FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS post_tags
(
  post_id    INT UNSIGNED,
  hashtag_id INT UNSIGNED,
  UNIQUE KEY `unique_post_hashtag` (post_id, hashtag_id)
);

CREATE INDEX user_login ON users (login);

-- Добавление столбцов в content_types

ALTER TABLE content_types
  ADD name VARCHAR(255);

ALTER TABLE content_types
  ADD priority INT UNSIGNED;

-- Создание индекса полнотекстового поиска

CREATE FULLTEXT INDEX post_ft_search ON posts (title, text);
