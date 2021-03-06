<?php
require_once 'mysql_helper.php';
require_once 'functions.php';
require_once 'init.php';
require_once 'connect.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

$user = $_SESSION['user'];
$userID = $user['id'];
$userData = isUserExist($connection, $userID);
if (!$userData) {
    exit('Произошла ошибка!');
}
$userName = $userData['name'];
$projects = getProjects($connection, $userData['id']);
$tasks = getTasks($connection, $userData['id']);

$newTaskName = '';
$newTaskProjectID = '';
$newTaskDate = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newTaskName = trim($_POST['name']) ?? '';
    $newTaskName = strip_tags($newTaskName);
    $newTaskProjectID = strip_tags($_POST['project']);
    $newTaskProjectID = $newTaskProjectID ?? '';
    $newTaskDate = strip_tags($_POST['date']) ?? '';

    $errors = [];
    if (empty($newTaskName)) {
        $errors['newTaskName'] = 'Название задачи не может быть пустым';
    }
    if (checkTaskExist($connection, $newTaskName, $userID, $newTaskProjectID)) {
        $errors['newTaskNameRepeat'] = 'Задача с таким названием уже существует';
    }
    if (!isProjectExist($newTaskProjectID)) {
        $errors['projectNotExists'] = 'Нужно выбрать проект';
    }

    if (!isCorrectDateFormat('d.m.Y', $newTaskDate)) {
        $errors['newTaskDate'] = 'Дата должна быть в формате ДД.ММ.ГГГГ';
    }
    if (checkPastDate($newTaskDate)) {
        $errors['newTaskDate'] = 'Дата не может быть раньше сегодняшнего дня';
    }

    if (isset($_FILES['preview'])) {
        $newTaskFileName = $_FILES['preview']['name'];
        if ($newTaskFileName) {
            $uniqueFileName = date('YmdHis') . '_' . $newTaskFileName;
        } else {
            $uniqueFileName = '';
        }
        $newTaskFilePathFull = __DIR__ . '\\' . $uniqueFileName;
        $res = move_uploaded_file($_FILES['preview']['tmp_name'], $newTaskFilePathFull);
        if ($newTaskFileName && !$res  && !is_readable($newTaskFileName)) {
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
        $stmt = db_get_prepare_stmt($connection, $addNewTask,
            [$newTaskName, $uniqueFileName, $newTaskDate, $userData['id'], $newTaskProjectID]);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: index.php');
            exit();
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
    'guestPage' => $guestPage,
    'user' => $user,
    'pageTitle' => $pageTitle,
    'userName' => $userName,
    'projects' => $projects,
    'mainContent' => $mainContent
]);

print($layout);
