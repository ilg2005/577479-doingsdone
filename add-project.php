<?php
require_once 'functions.php';
require_once 'init.php';
require_once 'connect.php';

$newProjectName = '';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}
$user = $_SESSION['user'];
$userID = $user['id'];
$userData = isUserExist($pdo, $userID);

if (!$userData) {
    exit('Произошла ошибка!');
}

$userName = $userData['name'];
$projects = getProjects($pdo, $userData['id']);
$tasks = getTasks($pdo, $userData['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newProjectName = strip_tags($_POST['name']);
    $newProjectName = trim($newProjectName) ?? '';

    $errors = [];
    if (empty($newProjectName)) {
        $errors['newProjectName'] = 'Название проекта не может быть пустым';
    }
    if (checkProjectExist($pdo, $newProjectName, $userID)) {
        $errors['newProjectNameRepeat'] = 'Проект с таким названием уже существует';
    }

    if (empty($errors)) {
        $addNewProject = 'INSERT INTO projects (name, user_id) VALUES (?, ?)';
        if (execute($pdo, $addNewProject, [$newProjectName, $userData['id']])) {
            header('Location: index.php');
            exit();
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

