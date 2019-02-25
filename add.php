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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newTaskName = htmlspecialchars($_POST['name']) ?? '';
    $newTaskProject = $_POST['project'] ?? '';
    $newTaskDate = htmlspecialchars($_POST['date']) ?? '';

} else {
    $newTaskName = '';
    $newTaskProject = '';
    $newTaskDate = '';
}

$mainContent = includeTemplate('add.php', [
    'projects' => $projects,
    'newTaskName' => $newTaskName,
    'newTaskProject' => $newTaskProject,
    'newTaskDate' => $newTaskDate
]);

$layout = includeTemplate('layout.php', [
    'pageTitle' => $pageTitle,
    'userName' => $userName,
    'projects' => $projects,
    'mainContent' => $mainContent
]);

print($layout);


?>
