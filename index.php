<?php
require_once('mysql_helper.php');
require_once('functions.php');
/*require_once('data.php');*/

$connection = connect2Database('localhost', 'root', '', 'doingsdone');

$userID = 4;
$userData = isUserExist($connection, $userID);

if ($connection && $userData) {
    $userName = $userData['name'];
    $projects = getSelectedUserProjects($connection, $userData['id']);
    $tasks = getSelectedUserTasks($connection, $userData['id']);
} else {
    die('Произошла ошибка!');
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
