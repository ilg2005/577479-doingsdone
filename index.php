<?php
require_once('mysql_helper.php');
require_once('functions.php');

$guestPage = false;
$show_complete_tasks = 1;
if(isset($_GET['show_completed'])) {
    $show_complete_tasks = htmlspecialchars($_GET['show_completed']);
}
session_start();
$connection = connect2Database('localhost', 'root', '', 'doingsdone');

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $userID = $user['id'];
    $userData = isUserExist($connection, $userID);
} else {
    header('Location: guest.php');
    exit();
}

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
