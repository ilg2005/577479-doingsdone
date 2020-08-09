<?php
require_once 'connect.php';
require_once 'functions.php';
require_once 'init.php';

if (!isset($_SESSION['user'])) {
    $guestPage = true;
    $mainContent = '';
    $guestPageContent = includeTemplate('guest.php', []);
} else {
    $user = $_SESSION['user'];
    $userID = $user['id'];

    $userData = isUserExist($pdo, $userID);
    if (!$userData) {
        exit('Произошла ошибка!');
    }

    $userName = $userData['name'];
    $projects = getProjects($pdo, $userData['id']);
    $tasks = getTasks($pdo, $userData['id']);

    $show_complete_tasks = 0;
    if (isset($_GET['show_completed'])) {
        $show_complete_tasks = htmlspecialchars($_GET['show_completed']);
    }

    if (isset($_GET['task_id'], $_GET['check'])) {
        changeTaskStatusInDatabase($pdo);
    }

    $searchText = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $searchText = strip_tags($_POST['text']);
        $searchText = trim($searchText) ?? '';
        $errors = [];
        if (empty($searchText)) {
            $errors['searchText'] = 'Поле поиска не может быть пустым';
        }
        $projectID = $_SESSION['project_id'] ?? '';
        $searchSql = 'SELECT * FROM tasks WHERE MATCH(name) AGAINST(? IN BOOLEAN MODE)';
        if ($projectID) {
            $searchInProjectSql = $searchSql . ' AND tasks.project_id = ?';
            $tasks = fetchData($pdo, $searchInProjectSql, [$searchText, $projectID]);
        } else {
            $tasks = fetchData($pdo, $searchSql, [$searchText]);
        }
    }

    $mainContent = includeTemplate('index.php', [
        'connection' => $pdo,
        'tasks' => $tasks,
        'show_complete_tasks' => $show_complete_tasks,
        'searchText' => $searchText,
        'errors' => $errors
    ]);
}

$layout = includeTemplate('layout.php', [
    'guestPage' => $guestPage,
    'guestPageContent' => $guestPageContent,
    'user' => $user,
    'pageTitle' => $pageTitle,
    'userName' => $userName,
    'projects' => $projects,
    'mainContent' => $mainContent
]);

print($layout);

