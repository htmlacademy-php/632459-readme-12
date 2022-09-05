<?php
require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';
require_once 'data.php';

if (!$_SESSION['user']) {
    header("Location: /");
    exit();
}

[$is_auth, $user_name, $page_titles] = require('data.php');
$con = require('init.php');

if (!$con) {
    $error = mysqli_connect_error();
    print("Ошибка подключения: " . $error);
    die();
}

$first_user = 1;
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

$all_dialogs = [];

foreach ($chats as $index => $chat) {
    if($chat['sender_id'] !== (string)$current_user) {
        $all_dialogs[$index] = $chat['sender_id'];
    } else if ($chat['reciever_id'] !== (string)$current_user) {
        $all_dialogs[$index] = $chat['reciever_id'];
    }
}

$dialogs_ids = array_unique($all_dialogs);

$page_content = include_template('messages.php',[
    'chat_messages' => $chat_messages
]);

$layout_content = include_template('layout.php', [
    'content'   => $page_content,
    'title'     => $page_titles['messages']
]);

print($layout_content);




