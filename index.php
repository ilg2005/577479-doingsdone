<?php
require_once('mysql_helper.php');
require_once('functions.php');

$guestPage = false;
$show_complete_tasks = 1;
$searchText = '';


if(isset($_GET['show_completed'])) {
    $show_complete_tasks = htmlspecialchars($_GET['show_completed']);
}
session_start();
$connection = connect2Database('localhost', 'root', '', 'doingsdone');

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $userID = $user['id'];
    $userData = isUserExist($connection, $userID);
    $errors = [];

} else {
    header('Location: guest.php');
    exit();
}

if ($connection && $userData) {
    $userName = $userData['name'];
    $projects = getProjects($connection, $userData['id']);
    $tasks = getTasks($connection, $userData['id']);
} else {
    die('Произошла ошибка!');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchText = trim($_POST['text']) ?? '';
    $searchText = htmlspecialchars($searchText);
    $errors = [];
    if (empty($searchText)) {
        $errors['searchText'] = 'Поле поиска не может быть пустым';
    } else {
        $searchSql = 'SELECT * FROM tasks WHERE MATCH(name) AGAINST(? IN BOOLEAN MODE)';
        $tasks = fetchData($connection, $searchSql, [$searchText]);
    }
}

$mainContent = includeTemplate('index.php', [
    'tasks' => $tasks,
    'show_complete_tasks' => $show_complete_tasks,
    'searchText' => $searchText,
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
