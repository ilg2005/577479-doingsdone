<?php
require_once('mysql_helper.php');
require_once('functions.php');

$isProjectsTasksPage = false;

$connection = connect2Database('localhost', 'root', '', 'doingsdone');

$userID = 4;
$userData = isUserExist($connection, $userID);

if ($connection && $userData) {
    $userName = $userData['name'];
} else {
    die('Произошла ошибка!');
}

$mainContent = includeTemplate('registration.php', []);

$layout = includeTemplate('layout.php', [
    'pageTitle' => $pageTitle,
    'mainContent' => $mainContent,
    'isProjectsTasksPage' => $isProjectsTasksPage
]);

print($layout);
?>
