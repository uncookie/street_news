<?php
//DO NOT ECHO ANYTHING ON THIS PAGE OTHER THAN RESPONSE
//'true' triggers login success
ob_start();
include 'config.php';
require 'includes/functions.php';

// Define $myusername and $mypassword
$username = $_POST['myusername'];
$password = $_POST['mypassword'];

// To protect MySQL injection
$username = stripslashes($username);
$password = stripslashes($password);

$response = '';
$loginCtl = new LoginForm;
$conf = new GlobalConf;
$lastAttempt = checkAttempts($username);
if (empty($lastAttempt)) {
    $attempts = '';
    $lastlogin = '';
} else {
    $attempts = isset($lastAttempt['attempts']) ? $lastAttempt['attempts'] : '';
    $lastlogin = isset($lastAttempt['lastlogin']) ? $lastAttempt['lastlogin'] : '';
}
$max_attempts = $conf->max_attempts;

//First Attempt
if (isset($lastlogin) and $lastlogin == '') {

    $lastlogin = 'never';
    $loginCtl->insertAttempt($username);
    $response = $loginCtl->checkLogin($username, $password);

} elseif (isset($attempts) and $attempts >= $max_attempts) {

    //Exceeded max attempts
    $loginCtl->updateAttempts($username);
    $response = $loginCtl->checkLogin($username, $password);

} else {

    $response = $loginCtl->checkLogin($username, $password);

}

if ($attempts < $max_attempts && $response != 'true') {

    $loginCtl->updateAttempts($username);
    $resp = new RespObj($username, $response);
    $jsonResp = json_encode($resp);
    echo $jsonResp;

} else {

    $resp = new RespObj($username, $response);
    $jsonResp = json_encode($resp);
    echo $jsonResp;

}

unset($resp, $jsonResp);
ob_end_flush();
