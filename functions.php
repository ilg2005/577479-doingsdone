<?php
const SECONDS_PER_DAY = 86400;
$pageTitle = 'Дела в порядке';

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

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
        if ($task['project_name'] === $projectName) {
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

function checkDatabaseError($link, $result)
{
    if (!$result) {
        print('Ошибка MySQL ' . mysqli_error($link));
        die();
    }
}

function connect2Database($hostName, $userName, $pwd, $dbName)
{
    $link = mysqli_connect($hostName, $userName, $pwd, $dbName);
    mysqli_set_charset($link, 'utf8');
    checkDatabaseError($link, $link);
    return $link;
}

function fetchData($link, $sql, $data = [])
{
    $result = [];
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    checkDatabaseError($link, $res);
    if ($res) {
        $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    }
    return $result;
}

function getSelectedUserName($link, $selectedUserID)
{
    $query = 'SELECT name FROM users WHERE id = ' . '?';
    $names = fetchData($link, $query, [$selectedUserID]);
    return $names[0]['name'];
}

function getSelectedUserProjects($link, $selectedUserID)
{
    $query = 'SELECT name FROM projects WHERE user_id = ' . '?';
    $projectsArray = fetchData($link, $query, [$selectedUserID]);
    foreach ($projectsArray as $project) {
        $projects[] = $project['name'];
    }
    return $projects;
}

function getSelectedUserTasks($link, $selectedUserID)
{
    $query = 'SELECT tasks.name, DATE_FORMAT(tasks.deadline, "%d.%m.%Y") AS deadline, projects.name AS project_name, tasks.is_done FROM tasks JOIN projects ON projects.id = tasks.project_id WHERE tasks.user_id = ' . '?';
    return fetchData($link, $query, [$selectedUserID]);
}

?>
