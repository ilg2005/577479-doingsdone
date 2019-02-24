<?php
require_once('mysql_helper.php');
require_once('functions.php');

$connection = connect2Database('localhost', 'root', '', 'doingsdone');

$userID = 4;
$userData = isUserExist($connection, $userID);

if ($connection && $userData) {
    $userName = $userData['name'];
    $projects = getProjects($connection, $userData['id']);
} else {
    die('Произошла ошибка!');
}

if (isset($_POST['name']) && isset($_POST['date'])) {
    $task = htmlspecialchars($_POST['name']) ?? '';
    $date = htmlspecialchars($_POST['date']) ?? '';
} else {
    $task = '';
    $date = '';
}

$mainContent = includeTemplate('add.php', [
    'projects' => $projects,
    'task' => $task,
    'date' => $date
]);

$layout = includeTemplate('layout.php', [
    'pageTitle' => $pageTitle,
    'userName' => $userName,
    'projects' => $projects,
    'mainContent' => $mainContent
]);

print($layout);


?>
