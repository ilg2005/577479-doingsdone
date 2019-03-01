<?php
require_once('mysql_helper.php');
require_once('functions.php');

$guestPage = false;
$show_complete_tasks = 1;
session_start();

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
} else {
    header('Location: guest.php');
    exit();
}

$connection = connect2Database('localhost', 'root', '', 'doingsdone');

$userID = $user['id'];
$userData = isUserExist($connection, $userID);

if ($connection && $userData) {
    $userName = $userData['name'];
    $projects = getProjects($connection, $userData['id']);
    $tasks = getTasks($connection, $userData['id']);
} else {
    die('Произошла ошибка!');
}

$mainContent = includeTemplate('index.php', [
    'tasks' => $tasks,
    'show_complete_tasks' => $show_complete_tasks
]);

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
