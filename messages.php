<?php

require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';
require_once 'data.php';

if (!isset($_SESSION['user'])) {
    header("Location: /");
    exit();
}

[$is_auth, $user_name, $page_titles, $validate_rules, $input_names, $month_list]
    = require('data.php');
$con = require('init.php');

if (!$con) {
    $error = mysqli_connect_error();
    print("Ошибка подключения: ".$error);
    die();
}

$first_user = (int)filter_input(INPUT_GET, 'user');

$current_user = $_SESSION['user']['id'];
$previous_page = $_SERVER['HTTP_REFERER'] ?? null;

$search_query = filter_input(INPUT_GET, 'search');
if (!empty($search_query)) {
    header("Location: /search.php?search=$search_query");
}

$unread = getUnreadMessages($con);

$sql_chats = 'SELECT sender_id, reciever_id, date_add, text, login, avatar_path FROM messages
JOIN users u ON sender_id = u.id
WHERE sender_id = '.$current_user.' OR reciever_id = '.$current_user.' ';

$result = formSqlRequest($con, $sql_chats, []);
$chats = mysqli_fetch_all($result, MYSQLI_ASSOC);

$dialogs_users = [];

$sql_dialog_users = ' SELECT MAX(ms.date_add) AS last_date, u.id, u.login, u.avatar_path, (SELECT m.text FROM messages m WHERE m.id = MAX(ms.id)) AS last_text,
 (SELECT sender_id FROM messages m WHERE m.id = MAX(ms.id)) AS sender
  FROM messages ms JOIN users u ON (u.id IN (ms.reciever_id, ms.sender_id)) WHERE (ms.reciever_id = ? OR ms.sender_id = ?) AND u.id != ? GROUP BY u.id ORDER BY last_date DESC;';
$result = formSqlRequest($con, $sql_dialog_users, [$current_user, $current_user, $current_user]);
$dialogs_users = mysqli_fetch_all($result, MYSQLI_ASSOC);

$unread_msg = [];

if (!empty($dialogs_users)) {
    $dialogs_ids = array_reduce(
        $dialogs_users,
        static function ($carry, $dialog) {
            $carry[] = $dialog['id'];

            return $carry;
        },
        []
    );

    $dialogs_ids_res = implode(",", $dialogs_ids);

    $sql_unread = 'SELECT COUNT(new) as total, sender_id FROM messages
            WHERE sender_id IN ('.$dialogs_ids_res.') AND reciever_id = ? GROUP BY sender_id';

    $result = formSqlRequest($con, $sql_unread, [$current_user]);
    $unread_messages = mysqli_fetch_all($result, MYSQLI_ASSOC);

    foreach ($unread_messages as $chat) {
        $unread_msg[$chat['sender_id']] = $chat['total'];
    }
}

$errors = [];
$is_dialog_exists = false;
$new_dialog = null;

foreach ($dialogs_users as $dialog) {
    if ($dialog['id'] === $first_user) {
        $is_dialog_exists = true;
    }
}

if (strripos($previous_page, 'profile.php') && !$is_dialog_exists) {
    $sql_new_dialog = 'SELECT id, login, avatar_path FROM users WHERE id = ?';
    $result = formSqlRequest($con, $sql_new_dialog, [$first_user]);
    if ($result) {
        $new_dialog = mysqli_fetch_all($result, MYSQLI_ASSOC);
        if (isset($new_dialog[0])) {
            array_push($dialogs_users, $new_dialog[0]);
        }
    }
}

if ($first_user === 0 && !empty($dialogs_users)) {
    $first_user = $dialogs_users[0]['id'];
}

$sql_chat_messages = 'SELECT sender_id, date_add, text, login, avatar_path FROM messages
JOIN users u ON sender_id = u.id
 WHERE sender_id IN('.$current_user.', ?) AND reciever_id IN ('.$current_user
    .', ?) ORDER BY DATE_ADD';

$result = formSqlRequest($con, $sql_chat_messages, [$first_user, $first_user]);

$chat_messages = mysqli_fetch_all($result, MYSQLI_ASSOC);

$sql_update_unread = 'UPDATE messages SET new = null WHERE reciever_id = ?';
$result = formSqlRequest(
    $con,
    $sql_update_unread,
    [$_SESSION['user']['id']],
    false
);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputArray = $_POST;
    $errors = validateForm($inputArray, $validate_rules, $con);

    if (empty($errors)) {
        $sql_message
            = 'INSERT INTO messages(date_add, text, sender_id, reciever_id, new) VALUES (NOW(), ?, ?, ?, 1)';

        $result = formSqlRequest(
            $con,
            $sql_message,
            [$inputArray['message'], $current_user, $first_user],
            false
        );

        header("Location: messages.php?user=".$first_user);
    }
}

$page_content = include_template('messages.php', [
    'chat_messages' => $chat_messages,
    'dialogs_users' => $dialogs_users,
    'first_user'    => $first_user,
    'errors'        => $errors,
    'month_list'    => $month_list,
    'unread_msg'    => $unread_msg,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title'   => $page_titles['messages'],
    'unread' => $unread
]);

print($layout_content);




