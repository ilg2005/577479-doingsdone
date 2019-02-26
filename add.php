<?php
require_once('mysql_helper.php');
require_once('functions.php');

$connection = connect2Database('localhost', 'root', '', 'doingsdone');

$userID = 4;
$userData = isUserExist($connection, $userID);

if ($connection && $userData) {
    $userName = $userData['name'];
    $projects = getProjects($connection, $userData['id']);
    $tasks = getTasks($connection, $userData['id']);
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
    if (checkTaskExist($tasks, $newTaskName)) {
        $errors['newTaskNameRepeat'] = 'Задача с таким названием уже существует';
    } else {
        $errors['newTaskNameRepeat'] = '';
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
        $newTaskFilePathFull = __DIR__ . '/' . $newTaskFileName;
        move_uploaded_file($_FILES['preview']['tmp_name'], $newTaskFilePathFull);
    }

    if($errors['newTaskName'] === '' && $errors['newTaskDate'] === '' && $errors['newTaskNameRepeat'] === '') {
        $addNewTask = 'INSERT INTO tasks (creation_date, is_done, name, file_name, deadline, user_id, project_id) VALUES (CURRENT_TIMESTAMP, 0, ?, ?, ?, ?, ?)';
        if ($newTaskDate) {
            $newTaskDate = date('Y-m-d', strtotime($newTaskDate));
        }
        $stmt = db_get_prepare_stmt($connection, $addNewTask, [$newTaskName, $newTaskFileName, $newTaskDate, $userData['id'], $newTaskProjectID ]);
        mysqli_stmt_execute($stmt);
        header('Location: index.php');
    }

} else {
    $newTaskName = '';
    $newTaskProjectID = '';
    $newTaskDate = '';
    $errors['newTaskName'] = '';
    $errors['newTaskNameRepeat'] = '';
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
