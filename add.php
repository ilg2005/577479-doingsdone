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

$mainContent = includeTemplate('add.php', [
    'projects' => $projects,
]);

$layout = includeTemplate('layout.php', [
    'pageTitle' => $pageTitle,
    'userName' => $userName,
    'projects' => $projects,
    'mainContent' => $mainContent
]);

print($layout);

if (isset($_POST['name'])) {
    $task = htmlspecialchars($_POST['name']);
}

?>
