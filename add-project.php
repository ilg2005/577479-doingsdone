<?php
require_once('mysql_helper.php');
require_once('functions.php');

$guestPage = false;
$errors = [];
$newProjectName = '';
session_start();

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $userID = $user['id'];
} else {
    header('Location: guest.php');
    exit();
}

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
    $newProjectName = trim($_POST['name']) ?? '';

    $errors = [];
    if (empty($newProjectName)) {
        $errors['newProjectName'] = 'Название проекта не может быть пустым';
    }
    if (checkProjectExist($connection, $newProjectName, $userID)) {
        $errors['newProjectNameRepeat'] = 'Проект с таким названием уже существует';
    }

    if (empty($errors)) {
        $addNewProject = 'INSERT INTO projects (name, user_id) VALUES (?, ?)';
        $stmt = db_get_prepare_stmt($connection, $addNewProject, [$newProjectName,  $userData['id']]);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: index.php');
        }
    }
}

$mainContent = includeTemplate('add-project.php', [
    'projects' => $projects,
    'newProjectName' => $newProjectName,
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

?>
