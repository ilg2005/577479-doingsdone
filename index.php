<?php
require_once 'mysql_helper.php';
require_once 'functions.php';
require_once 'init.php';

if (!isset($_SESSION['user'])) {
    $guestPage = true;
    $mainContent = '';
    $guestPageContent = includeTemplate('guest.php', []);
} else {
    $user = $_SESSION['user'];
    $userID = $user['id'];

    $connection = connect2Database('localhost', 'root', '', 'doingsdone');
    $userData = isUserExist($connection, $userID);
    if (!$connection && !$userData) {
        exit('Произошла ошибка!');
    }

    $userName = $userData['name'];
    $projects = getProjects($connection, $userData['id']);
    $tasks = getTasks($connection, $userData['id']);

    $show_complete_tasks = 0;
    if (isset($_GET['show_completed'])) {
        $show_complete_tasks = htmlspecialchars($_GET['show_completed']);
    }

    if (isset($_GET['task_id'], $_GET['check'])) {
        changeTaskStatusInDatabase();
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
            $tasks = fetchData($connection, $searchInProjectSql, [$searchText, $projectID]);
        } else {
            $tasks = fetchData($connection, $searchSql, [$searchText]);
        }
    }

    $mainContent = includeTemplate('index.php', [
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

