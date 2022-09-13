<?php
require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';
require_once 'data.php';

if (!$_SESSION['user']) {
    header("Location: /");
    exit();
}

[$is_auth, $user_name, $page_titles, $validate_rules] = require('data.php');
$con = require('init.php');

if (!$con) {
    $error = mysqli_connect_error();
    print("Ошибка подключения: " . $error);
    die();
}

$first_user = (int)filter_input(INPUT_GET, 'user');

$current_user = $_SESSION['user']['id'];

$search_query = filter_input(INPUT_GET, 'search');
if (!empty($search_query)) {
    header("Location: /search.php?search=$search_query");
}

$sql_chat_messages = 'SELECT sender_id, date_add, text, login, avatar_path FROM messages
JOIN users u ON sender_id = u.id
 WHERE sender_id IN(' . $current_user . ', ?) AND reciever_id IN (' . $current_user . ', ?) ORDER BY DATE_ADD';

$result = form_sql_request($con, $sql_chat_messages, [$first_user, $first_user]);

$chat_messages = mysqli_fetch_all($result, MYSQLI_ASSOC);

$sql_chats = 'SELECT sender_id, reciever_id, date_add, text, login, avatar_path FROM messages
JOIN users u ON sender_id = u.id
WHERE sender_id = ' . $current_user. ' OR reciever_id = '. $current_user . ' ';

$result = form_sql_request($con, $sql_chats, []);
$chats = mysqli_fetch_all($result, MYSQLI_ASSOC);

$dialogs_users = [];

$sql_dialog_users = ' SELECT MAX(ms.date_add) AS last_date, u.id, u.login, u.avatar_path, (SELECT m.text FROM messages m WHERE m.id = MAX(ms.id)) AS last_text,
 (SELECT sender_id FROM messages m WHERE m.id = MAX(ms.id)) AS sender
  FROM messages ms JOIN users u ON (u.id IN (ms.reciever_id, ms.sender_id)) AND u.id != ? GROUP BY u.id ORDER BY last_date DESC;';
    $result = form_sql_request($con, $sql_dialog_users, [$current_user]);
    $dialogs_users = mysqli_fetch_all($result, MYSQLI_ASSOC);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputArray = $_POST;
    $errors = validateForm($inputArray, $validate_rules, $con);

    if (empty($errors)) {
        $sql_message = 'INSERT INTO messages(date_add, text, sender_id, reciever_id) VALUES (NOW(), ?, ?, ?)';

        $result = form_sql_request($con, $sql_message, [$inputArray['message'], $current_user, $first_user], false);

        header("Location: messages.php?user=" . $first_user);
    }
}

$page_content = include_template('messages.php',[
    'chat_messages' => $chat_messages,
    'dialogs_users' => $dialogs_users,
    'first_user' => $first_user,
    'errors' => $errors
]);

$layout_content = include_template('layout.php', [
    'content'   => $page_content,
    'title'     => $page_titles['messages']
]);

print($layout_content);




