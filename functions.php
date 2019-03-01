<?php
const SECONDS_PER_DAY = 86400;
$pageTitle = 'Дела в порядке';

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

function fetchRow($link, $sql, $data = [])
{
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_array($res);
}

function isUserExist($link, $selectedUserID)
{
    $query = 'SELECT * FROM users WHERE id = ? LIMIT 1';
    $result = fetchRow($link, $query, [$selectedUserID]);
    if ($result !== []) {
        return $result;
    }
    return null;
}

function getProjects($link, $selectedUserID)
{
    $query = 'SELECT p.id, p.name, count(t.id) as task_count FROM projects p LEFT JOIN tasks t ON t.project_id = p.id WHERE p.user_id = ? GROUP BY p.id';
    return fetchData($link, $query, [$selectedUserID]);
}

function showNotFound()
{
    header('HTTP/1.1 404 Not Found');
    die();
}

function getTasks($link, $selectedUserID)
{
    $projectDataByID = null;
    $projectData = 'SELECT * FROM projects WHERE user_id = ? AND id = ?';
    $allTasks = 'SELECT t.id, t.name, t.file_name, DATE_FORMAT(t.deadline, "%d.%m.%Y") AS deadline,  t.is_done FROM tasks t WHERE t.user_id = ?';
    $projectSpecificTasks = $allTasks . ' AND t.project_id = ?';

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

function changeTaskStatusInDatabase()
{
    $connection = connect2Database('localhost', 'root', '', 'doingsdone');

    if (isset($_GET['task_id']) && isset($_GET['check'])) {
        $taskID = $_GET['task_id'];
        $status = $_GET['check'];

        $taskStatusUpdate = 'UPDATE tasks SET is_done = ? WHERE id = ?';
        $stmt = db_get_prepare_stmt($connection, $taskStatusUpdate, [$status, $taskID]);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: /index.php?show_completed=1');
        }
    }
}

function isCorrectDateFormat($format, $date)
{
    return (!$date || date_create_from_format($format, $date));
}

function checkPastDate($date)
{
    return (strtotime($date) < mktime(0, 0, 0) && $date !== '');
}

function checkTaskExist($link, $taskName, $userID)
{
    $sql = 'SELECT id FROM tasks WHERE name = ? AND user_id = ? LIMIT 1';
    $result = fetchRow($link, $sql, [$taskName, $userID]);
    return $result;
}

function checkProjectExist($link, $projectName, $userID)
{
    $sql = 'SELECT id FROM projects WHERE name = ? AND user_id = ? LIMIT 1';
    $result = fetchRow($link, $sql, [$projectName, $userID]);
    return $result;
}

function isEmailValid($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function applyBulkFilter($userID, $filter) {
    $connection = connect2Database('localhost', 'root', '', 'doingsdone');

    $allSql = 'SELECT * FROM tasks t WHERE t.user_id = ?';
    $todaySql = $allSql . ' AND t.deadline = CURDATE()';
    $tomorrowSql = $allSql . ' AND t.deadline = CURDATE() + 1';
    $overdueSql = $allSql . ' AND t.deadline < CURDATE() AND t.is_done = 0';

    $filters = [
        'all' => $allSql,
        'today' => $todaySql,
        'tomorrow' => $tomorrowSql,
        'overdue' => $overdueSql
    ];

    $filteredTasks = fetchData($connection, $filters[$filter], [$userID]);
        if ($filteredTasks) {
            return $filteredTasks;
        }
    }

?>
