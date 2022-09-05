<?php
require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';

if (!$con) {
    $error = mysqli_connect_error();
    print("Ошибка подключения: " . $error);
    die();
}

[$is_auth, $user_name, $page_titles, $validate_rules, $input_names] = require('data.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputArray = $_POST;
    $errors = validateForm($inputArray, $validate_rules, $con);

    if (empty($errors)) {
        $sql_message = 'INSERT INTO messages VALUES (NOW(), ?, ?, ?)';
    }
}



