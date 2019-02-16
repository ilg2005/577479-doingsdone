<?php
const SECONDS_PER_DAY = 86400;

function includeTemplate($name, $data)
{
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

function countTasks4Projects($tasksArray, $projectName)
{
    $count = 0;
    foreach ($tasksArray as $task) {
        if ($task['projectCategory'] === $projectName) {
            ++$count;
        }
    }
    return $count;
}

function filterUserInput($tasksArray)
{
    $filteredArray = [];
    foreach ($tasksArray as $task) {
        $filteredTask = array_map('htmlspecialchars', $task);
        $filteredArray[] = $filteredTask;
    }
    return $filteredArray;
}

function checkTaskImportant($deadline)
{
    if (strtotime($deadline)) {
        $timeRemaining = strtotime($deadline) - time();
        return ($timeRemaining <= SECONDS_PER_DAY);
    }
}


function connect2Database($hostName, $userName, $pwd, $dbName) {
    $connector = mysqli_connect($hostName, $userName, $pwd, $dbName);
    mysqli_set_charset($connector, 'utf8');
    if (!$connector) {
        print('Ошибка пожключения к MySQL ' . mysqli_connect_error());
        die();
    }
    return $connector;
}

function fetchData($link, $sql, $data = []) {
    $result = [];
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    }
    return $result;
}


?>
