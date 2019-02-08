<?php
require_once('functions.php');
require_once('config.php');
require_once('data.php');

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);
$pageTitle = $config['pageTitle'];

if ($config['enable']) {
    $mainContent = $includeTemplate('main.php', [
        'tasks' => $tasks,
        'show_complete_tasks' => $show_complete_tasks
    ]);

    $layout = $includeTemplate('layout.php', [
        'pageTitle' => $pageTitle,
        'projects' => $projects,
        'mainContent' => $mainContent,
    ]);
}
print($layout);

?>
