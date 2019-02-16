<?php
require_once('functions.php');
require_once('data.php');

$connector = mysqli_connect('localhost', 'root', '', 'doingsdone');
mysqli_set_charset($connector, 'utf8');
if (!$connector) {
    print('Ошибка подключения: ' . mysqli_connect_error());
} else {
    $selectUser = 'SELECT name FROM users';
    $result = mysqli_query($connector, $selectUser);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    print($rows[1]);
    if (!$result) {
        $error = mysqli_error($connector);
        print('Ошибка MySQL: . $error');
    }
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
