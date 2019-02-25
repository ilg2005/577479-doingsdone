<?php
require_once('mysql_helper.php');
require_once('functions.php');

$connection = connect2Database('localhost', 'root', '', 'doingsdone');

$userID = 4;
$userData = isUserExist($connection, $userID);

if ($connection && $userData) {
    $userName = $userData['name'];
    $projects = getProjects($connection, $userData['id']);
} else {
    die('Произошла ошибка!');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newTaskName = htmlspecialchars($_POST['name']) ?? '';
    $newTaskProjectID = $_POST['project'] ?? '';
    $newTaskDate = htmlspecialchars($_POST['date']) ?? '';

    $errors = [];
    if (checkFieldEmpty($newTaskName)) {
        $errors['newTaskName'] = 'Название задачи не может быть пустым';
    } else {
        $errors['newTaskName'] = '';
    }
    if (checkDateFormat($newTaskDate)) {
        $errors['newTaskDate'] = '';
    } else {
        $errors['newTaskDate'] = 'Дата должна быть в формате ДД.ММ.ГГГГ';
    }
    if (checkFutureDate($newTaskDate)) {
        $errors['newTaskDate'] = '';
    } else {
        $errors['newTaskDate'] = 'Дата не может быть раньше сегодняшнего дня';
    }

    if (isset($_FILES['preview'])) {
        $newTaskFileName = $_FILES['preview']['name'];
        $newTaskFilePath = __DIR__ . '/' . $newTaskFileName;
        move_uploaded_file($_FILES['preview']['tmp_name'], $newTaskFilePath);
    }

    if(!count($errors)) {
        $addNewTask = 'INSERT INTO tasks (creation_date, is_done, name, file_name, file_path, deadline, user_id, project_id) VALUES (CURRENT_TIMESTAMP, 0, $newTaskName, $newTaskFileName, $newTaskFilePath, $newTaskDate, $userData["id"], $newTaskProjectID)';

    }

} else {
    $newTaskName = '';
    $newTaskProjectID = '';
    $newTaskDate = '';
    $errors['newTaskName'] = '';
    $errors['newTaskDate'] = '';
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
    'mainContent' => $mainContent
]);

print($layout);


?>
