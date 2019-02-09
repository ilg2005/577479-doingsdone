<?php
function includeTemplate($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

function countTasks4Projects($tasksArray, $projectName) {
    $count = 0;
    foreach ($tasksArray as $task) {
        if ($task['projectCategory'] === $projectName) {
            ++$count;
        }
    }
    return $count;
}

function filterUserInput($tasksArray) {
    $filteredArray = [];
    foreach($tasksArray as $task) {
        $filteredTask = array_map('htmlspecialchars', $task);
        $filteredArray[] = $filteredTask;
    }
    return $filteredArray;
}
?>
