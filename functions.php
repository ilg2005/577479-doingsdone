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

function isUserExist($link, $selectedUserID)
{
    $query = 'SELECT * FROM users WHERE id = ? LIMIT 1';
    $result = fetchData($link, $query, [$selectedUserID]);
    if ($result !== []) {
        return $result[0];
    }
    return null;
}

function getProjects($link, $selectedUserID)
{
    $query = 'SELECT p.id, p.name, count(t.id) as task_count FROM projects p LEFT JOIN tasks t ON t.project_id = p.id WHERE p.user_id = ? GROUP BY p.id';
    return fetchData($link, $query, [$selectedUserID]);
}

function showNotFound() {
    header('HTTP/1.1 404 Not Found');
    die();
}

function getTasks($link, $selectedUserID)
{
    $projectDataByID = NULL;
    $projectData = 'SELECT * FROM projects WHERE user_id = ? AND id = ?';
    $allTasks = 'SELECT tasks.name, DATE_FORMAT(tasks.deadline, "%d.%m.%Y") AS deadline,  tasks.is_done FROM tasks WHERE tasks.user_id = ?';
    $projectSpecificTasks = $allTasks . ' AND tasks.project_id = ?';

    if (isset($_GET['project_id'])) {
        if (!is_numeric($_GET['project_id']) || $_GET['project_id'] === '') {
            showNotFound();
        }
        if (!($projectDataByID = fetchData($link, $projectData, [$selectedUserID, $_GET['project_id']]))) {
            showNotFound();
        }
        $tasks = fetchData($link, $projectSpecificTasks, [$selectedUserID, $_GET['project_id']]);
    } else {
        $tasks = fetchData($link, $allTasks, [$selectedUserID]);
    }

    return $tasks;
}

function checkDateFormat($date) {
    $result = false;
    $regexp = '/(\d{2})\.(\d{2})\.(\d{4})/m';
    if (preg_match($regexp, $date, $parts) && count($parts) == 4) {
        $result = checkdate($parts[2], $parts[1], $parts[3]);
    }
    return $result;
}

function checkFieldNotEmpty($field) {
    $regexp = '/^$/';
    $result = !preg_match($regexp, trim($field));
    return $result;
}
?>
