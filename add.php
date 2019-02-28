<?php
require_once('mysql_helper.php');
require_once('functions.php');

$isProjectsTasksPage = true;

$newTaskName = '';
$newTaskProjectID = '';
$newTaskDate = '';
$errors = [];
$userID = 4;

$connection = connect2Database('localhost', 'root', '', 'doingsdone');

$userData = isUserExist($connection, $userID);

if ($connection && $userData) {
    $userName = $userData['name'];
    $projects = getProjects($connection, $userData['id']);
    $tasks = getTasks($connection, $userData['id']);
} else {
    die('Произошла ошибка!');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newTaskName = trim($_POST['name']) ?? '';
    $newTaskName = htmlspecialchars($newTaskName);
    $newTaskProjectID = $_POST['project'] ?? '';
    $newTaskDate = htmlspecialchars($_POST['date']) ?? '';

    $errors = [];
    if (empty($newTaskName)) {
        $errors['newTaskName'] = 'Название задачи не может быть пустым';
    }
    if (checkTaskExist($connection, $newTaskName)) {
        $errors['newTaskNameRepeat'] = 'Задача с таким названием уже существует';
    }
    if (!isCorrectDateFormat('d.m.Y', $newTaskDate)) {
        $errors['newTaskDate'] = 'Дата должна быть в формате ДД.ММ.ГГГГ';
    }
    if (checkPastDate($newTaskDate)) {
        $errors['newTaskDate'] = 'Дата не может быть раньше сегодняшнего дня';
    }

    if (isset($_FILES['preview'])) {
        $newTaskFileName = date('YmdHis') . '_' . $_FILES['preview']['name'];
        $newTaskFilePathFull = __DIR__ . '/' . $newTaskFileName;
        $res = move_uploaded_file($_FILES['preview']['tmp_name'], $newTaskFilePathFull);
        if ($newTaskFileName && !$res) {
            $errors['fileSave'] = 'Файл не загружен';
        }
    }

    if (empty($errors)) {
        $addNewTask = 'INSERT INTO tasks (creation_date, is_done, name, file_name, deadline, user_id, project_id) VALUES (NOW(), 0, ?, ?, ?, ?, ?)';
        if ($newTaskDate) {
            $newTaskDate = date('Ymd', strtotime($newTaskDate));
        } else {
            $newTaskDate = 0;
        }
        $stmt = db_get_prepare_stmt($connection, $addNewTask, [$newTaskName, $newTaskFileName, $newTaskDate, $userData['id'], $newTaskProjectID]);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: index.php');
        }
    }
}

$mainContent = includeTemplate('add.php', [
    'projects' => $projects,
    'newTaskName' => $newTaskName,
    'newTaskProjectID' => $newTaskProjectID,
    'newTaskDate' => $newTaskDate,
    'errors' => $errors
]);

$layout = includeTemplate('layout.php', [
    'pageTitle' => $pageTitle,
    'userName' => $userName,
    'projects' => $projects,
    'mainContent' => $mainContent,
    'isProjectsTasksPage' => $isProjectsTasksPage
]);

print($layout);

?>
