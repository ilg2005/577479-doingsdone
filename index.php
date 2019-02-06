<?php
require_once('functions.php');
require_once('config.php');
require_once('data.php');

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$countTasks4Projects = function ($tasksArray, $projectName) {
    $count = 0;
    foreach ($tasksArray as $item) {
        ($item['projectCategory'] === $projectName) ? ($count++) : '';
    }
    return $count;
};
?>
