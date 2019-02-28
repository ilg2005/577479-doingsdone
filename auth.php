<?php
require_once('mysql_helper.php');
require_once('functions.php');

$guestPage = false;
$connection = connect2Database('localhost', 'root', '', 'doingsdone');
$user = [];
$userName = '';
$projects = [];

//$userData = isUserExist($connection, $userID);

/*if ($connection && $userData) {
    $userName = $userData['name'];
    $projects = getProjects($connection, $userData['id']);
    $tasks = getTasks($connection, $userData['id']);
} else {
    die('Произошла ошибка!');
}*/

$mainContent = includeTemplate('auth.php', []);

$layout = includeTemplate('layout.php', [
    'guestPage' => $guestPage,
    'user' => $user,
    'pageTitle' => $pageTitle,
    'userName' => $userName,
    'projects' => $projects,
    'mainContent' => $mainContent
]);

print($layout);
?>
