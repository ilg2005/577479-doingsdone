<?php
require_once('functions.php');
require_once('data.php');

$connection = connect2Database('localhost', 'root', '', 'doingsdone');

$userID = 4;
if ($connection) {
    $userName = getSelectedUserName($connection, $userID);
    $projects = getSelectedUserProjects($connection, $userID);
}

$tasks = filterUserInput($tasks);
$mainContent = includeTemplate('index.php', [
    'tasks' => $tasks,
    'show_complete_tasks' => $show_complete_tasks
]);

$layout = includeTemplate('layout.php', [
    'pageTitle' => $pageTitle,
    'userName' => $userName,
    'projects' => $projects,
    'tasks' => $tasks,
    'mainContent' => $mainContent
]);

print($layout);

?>
