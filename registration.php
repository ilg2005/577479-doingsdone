<?php
require_once('mysql_helper.php');
require_once('functions.php');

$isProjectsTasksPage = false;
$email = '';
$password = '';
$userName = '';
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
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $userName = $_POST['name'] ?? '';

    $requiredFields = [
        'email' => $email,
        'password' => $password,
        'name' => $userName
    ];

    $errors = [];
    foreach ($requiredFields as $key => $value) {
        if (empty(trim($value))) {
            $errors[$key] = 'Это поле нужно заполнить';
        }
    }
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
        $sql = 'SELECT id FROM users WHERE email = ? LIMIT 1';
        $result = fetchData($connection, $sql, [$email]);
        if ($result) {
            $errors['email'] = 'Пользователь с таким email уже зарегистрирован';
        }
    }
    /*$addNewTask = 'INSERT INTO tasks (creation_date, is_done, name, file_name, deadline, user_id, project_id) VALUES (CURRENT_TIMESTAMP, 0, ?, ?, ?, ?, ?)';
    if ($newTaskDate) {
        $newTaskDate = date('Ymd', strtotime($newTaskDate));
    } else {
        $newTaskDate = 0;
    }
    $stmt = db_get_prepare_stmt($connection, $addNewTask, [$newTaskName, $newTaskFileName, $newTaskDate, $userData['id'], $newTaskProjectID]);
    mysqli_stmt_execute($stmt);*/

    if (empty($errors)) {
        header('Location: index.php');
    }
}

$mainContent = includeTemplate('registration.php', [
    'email' => $email,
    'password' => $password,
    'userName' => $userName,
    'errors' => $errors
]);

$layout = includeTemplate('layout.php', [
    'pageTitle' => $pageTitle,
    'mainContent' => $mainContent,
    'isProjectsTasksPage' => $isProjectsTasksPage
]);

print($layout);
?>
