<?php

require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';

define('TYPE_QUOTE', 'quote');
define('TYPE_TEXT', 'text');
define('TYPE_PHOTO', 'photo');
define('TYPE_VIDEO', 'video');
define('TYPE_LINK', 'link');

if (!$_SESSION['user']) {
    header("Location: /");
    exit();
}

[$is_auth, $user_name, $page_titles, $validate_rules, $input_names]
    = require('data.php');

if (!$con) {
    $error = mysqli_connect_error();
    print("Ошибка подключения: ".$error);
    die();
}

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

require 'vendor/autoload.php';
$dsn
    = 'smtp://c60eabd512126a:f48119633abff7@smtp.mailtrap.io:2525?encryption=tls&auth_mode=login';
$transport = Transport::fromDsn($dsn);

$search_query = filter_input(INPUT_GET, 'search');
if (!empty($search_query)) {
    header("Location: /search.php?search=$search_query");
}

$unread = getUnreadMessages($con);

$sql_types = 'SELECT id, type, name FROM content_types ORDER BY priority';
$result = formSqlRequest($con, $sql_types, []);
$types = mysqli_fetch_all($result, MYSQLI_ASSOC);

$type = filter_input(INPUT_GET, 'type');

$type_id = getTypeId($types, $type);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputArray = array_merge($_GET, $_POST, $_FILES);
    $errors = validateForm($inputArray, $validate_rules, $con);

    if (empty($errors)) {
        switch ($type) {
            case TYPE_QUOTE:
                $sql_post
                    = 'INSERT INTO posts (date_add, user_id, show_count, content_type, title, text, cite_author) VALUES (NOW(), ?, 0, ?, ?, ?, ?)';
                $params = [
                    $_SESSION['user']['id'],
                    $type_id,
                    $inputArray['title'],
                    $inputArray['cite-text'],
                    $inputArray['quote-author'],
                ];
                $result = formSqlRequest($con, $sql_post, $params, false);

                if ($result) {
                    $new_post_id = mysqli_insert_id($con);
                    getHashtags($inputArray, $con, $new_post_id);
                    header("Location: post.php?post=".$new_post_id);
                }

                break;

            case TYPE_TEXT:
                $sql_post
                    = 'INSERT INTO posts (date_add, user_id, show_count, content_type, title, text) VALUES (NOW(), ?, 0, ?, ?, ?)';
                $params = [
                    $_SESSION['user']['id'],
                    $type_id,
                    $inputArray['title'],
                    $inputArray['text'],
                ];

                $result = formSqlRequest($con, $sql_post, $params, false);

                if ($result) {
                    $new_post_id = mysqli_insert_id($con);
                    getHashtags($inputArray, $con, $new_post_id);
                    header("Location: post.php?post=".$new_post_id);
                }

                break;

            case TYPE_PHOTO:
                $file_field = 'image';

                if (file_exists($inputArray[$file_field]['tmp_name'])
                    || is_uploaded_file($inputArray[$file_field]['tmp_name'])
                ) {
                    $img_path = getUploadedFile($inputArray, $file_field);
                } else {
                    $img_path = getUrlContent($inputArray);
                }

                $sql_post
                    = 'INSERT INTO posts (date_add, user_id, show_count, content_type, title, img) VALUES (NOW(), ?, 0, ?, ?, ?)';
                $params = [
                    $_SESSION['user']['id'],
                    $type_id,
                    $inputArray['title'],
                    $img_path,
                ];

                $result = formSqlRequest($con, $sql_post, $params, false);

                if ($result) {
                    $new_post_id = mysqli_insert_id($con);
                    getHashtags($inputArray, $con, $new_post_id);
                    header("Location: post.php?post=".$new_post_id);
                }

                break;

            case TYPE_VIDEO:
                $sql_post
                    = 'INSERT INTO posts (date_add, user_id, show_count, content_type, title, video) VALUES (NOW(), ?, 0, ?, ?, ?)';
                $params = [
                    $_SESSION['user']['id'],
                    $type_id,
                    $inputArray['title'],
                    $inputArray['video'],
                ];

                $result = formSqlRequest($con, $sql_post, $params, false);

                if ($result) {
                    $new_post_id = mysqli_insert_id($con);
                    getHashtags($inputArray, $con, $new_post_id);
                    header("Location: post.php?post=".$new_post_id);
                }

                break;

            case TYPE_LINK:
                $sql_post
                    = 'INSERT INTO posts (date_add, user_id, show_count, content_type, title, link) VALUES (NOW(), ?, 0, ?, ?, ?)';
                $params = [
                    $_SESSION['user']['id'],
                    $type_id,
                    $inputArray['title'],
                    $inputArray['link'],
                ];

                $result = formSqlRequest($con, $sql_post, $params, false);

                if ($result) {
                    $new_post_id = mysqli_insert_id($con);
                    getHashtags($inputArray, $con, $new_post_id);

                    $sql_subscribers
                        = 'SELECT u.login, u.email FROM subscriptions '
                        .'JOIN users u ON u.id = subscribe_id '
                        .'WHERE u.id = ?';

                    $result = formSqlRequest(
                        $con,
                        $sql_subscribers,
                        [$_SESSION['user']['id']]
                    );
                    $subscribers = mysqli_fetch_all($result, MYSQLI_ASSOC);

                    foreach ($subscribers as $sub) {
                        $message = new Email();
                        $message->to($sub['email']);
                        $message->from("mail@readme.com");
                        $message->subject(
                            "Новая публикация от пользователя %логин автора поста%"
                        );
                        $message->text(
                            "ЗЗдравствуйте, ${sub['login']}.
                            Пользователь ${$_SESSION['user'['login']]} только что опубликовал новую запись ${$inputArray['title']}.
                            Посмотрите её на странице пользователя: http://readme/profile.php?user=${$_SESSION['user']['id']}&tab=posts"
                        );
                        $mailer = new Mailer($transport);
                        try {
                            $mailer->send($message);
                        } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
                        }
                    }

                    header("Location: post.php?post=".$new_post_id);
                }

                break;
        }
    }
}

$title_input = include_template('title-input.php', [
    'errors'      => $errors,
    'input_names' => $input_names,
]);

$tags_input = include_template('tags-input.php', [
    'errors'      => $errors,
    'input_names' => $input_names,
]);

$page_content = include_template('add.php', [
    'types'       => $types,
    'title_input' => $title_input,
    'tags_input'  => $tags_input,
    'errors'      => $errors,
    'input_names' => $input_names,
    'unread'      => $unread,
]);

print($page_content);
