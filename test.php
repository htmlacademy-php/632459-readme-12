<?php
 require_once 'init.php';
 require_once 'helpers.php';
 require_once 'functions.php';
 require_once 'data.php';

//  $validate_rules = [
//      'link' => 'required_if:link'
//  ];




 $inputArray = array_merge($_GET, $_POST, $_FILES);
 var_dump(validateForm($inputArray, $validate_rules, $con));
