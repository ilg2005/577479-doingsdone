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

    if (isset($_GET['project_id'])) {
        $result = mysqli_query($connection, 'SELECT * FROM projects WHERE user_id = ' . $userData['id'] . ' AND projects.id = ' . htmlspecialchars($_GET['project_id']));
        if ($_GET['project_id'] === '' || !mysqli_fetch_row($result)) {
            header('HTTP/1.0 404 Not Found');
            die();
        }
        $tasks = getTasks4Project($connection, $userData['id'], $_GET['project_id']);
    } else {
        $tasks = getSelectedUserTasks($connection, $userData['id']);
    }
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
