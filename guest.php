<?php
require_once('mysql_helper.php');
require_once('functions.php');

session_start();
$guestPage = true;
$user = [];
$mainContent = '';

$connection = connect2Database('localhost', 'root', '', 'doingsdone');

$guestPageContent = includeTemplate('guest.php', []);

$layout = includeTemplate('layout.php', [
    'guestPage' => $guestPage,
    'guestPageContent' => $guestPageContent,
    'user' => $user,
    'pageTitle' => $pageTitle,
    'userName' => '',
    'projects' => [],
    'mainContent' => $mainContent,
]);

print($layout);

?>
