<?php
require_once __DIR__ . '/includes/config.php';

session_start();

if (isset($_GET['code'])) {

    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    $google_oauth = new Google_Service_Oauth2($client);
    $userInfo = $google_oauth->userinfo->get();

    $email = $userInfo->email;
    $name = $userInfo->name;

    // TODO: check user in database
    // if not exist → insert user

    $_SESSION['login'] = $email;
    $_SESSION['username'] = $name;

    header("Location: index.php");
    exit();
}