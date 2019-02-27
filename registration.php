<?php
require_once('mysql_helper.php');
require_once('functions.php');

$isProjectsTasksPage = false;
$errors = [];

$connection = connect2Database('localhost', 'root', '', 'doingsdone');

/*$userID = 4;
$userData = isUserExist($connection, $userID);

if ($connection && $userData) {
    $userName = $userData['name'];
} else {
    die('Произошла ошибка!');
}*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requiredFields = ['email', 'password', 'name'];

    $userEmail = trim($_POST['email']) ?? '';
    $userPassword = $_POST['password'] ?? '';
    $userName = htmlspecialchars($_POST['name']) ?? '';

    $errors = [];
    /*if (empty($newTaskName)) {
        $errors['newTaskName'] = 'Название задачи не может быть пустым';
    }
    if (checkTaskExist($tasks, $newTaskName)) {
        $errors['newTaskNameRepeat'] = 'Задача с таким названием уже существует';
    }
    if (checkWrongDateFormat($newTaskDate)) {
        $errors['newTaskDate'] = 'Дата должна быть в формате ДД.ММ.ГГГГ';
    }
    if (checkPastDate($newTaskDate)) {
        $errors['newTaskDate'] = 'Дата не может быть раньше сегодняшнего дня';
    }*/

    if (empty($errors)) {
        /*$addNewTask = 'INSERT INTO tasks (creation_date, is_done, name, file_name, deadline, user_id, project_id) VALUES (CURRENT_TIMESTAMP, 0, ?, ?, ?, ?, ?)';
        if ($newTaskDate) {
            $newTaskDate = date('Ymd', strtotime($newTaskDate));
        } else {
            $newTaskDate = 0;
        }
        $stmt = db_get_prepare_stmt($connection, $addNewTask, [$newTaskName, $newTaskFileName, $newTaskDate, $userData['id'], $newTaskProjectID]);
        mysqli_stmt_execute($stmt);*/
        header('Location: index.php');
    }
}

$mainContent = includeTemplate('registration.php', []);

$layout = includeTemplate('layout.php', [
    'pageTitle' => $pageTitle,
    'mainContent' => $mainContent,
    'isProjectsTasksPage' => $isProjectsTasksPage
]);

print($layout);
?>
