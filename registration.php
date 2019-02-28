<?php
require_once('mysql_helper.php');
require_once('functions.php');

$isProjectsTasksPage = false;
$email = '';
$password = '';
$user = [];
$userName = '';
$guestPage = false;
$errors = [];


$connection = connect2Database('localhost', 'root', '', 'doingsdone');

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

    if (empty($errors)) {
        if (!isEmailValid($email)) {
            $errors['email'] = 'E-mail введен некорректно';
        }

        $sql = 'SELECT id FROM users WHERE email = ? LIMIT 1';
        $result = fetchData($connection, $sql, [$email]);
        if ($result) {
            $errors['email'] = 'Пользователь с таким email уже зарегистрирован';
        }
    }

    if (empty($errors)) {
        $password = password_hash($password, PASSWORD_DEFAULT);

        $addNewUser = 'INSERT INTO users (registration_date, email, name, password) VALUES (NOW(), ?, ?, ?)';
        $stmt = db_get_prepare_stmt($connection, $addNewUser, [$email, $userName, $password]);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: index.php');
        }
    }
}

$mainContent = includeTemplate('registration.php', [
    'email' => $email,
    'password' => $password,
    'userName' => $userName,
    'errors' => $errors
]);

$layout = includeTemplate('layout.php', [
    'guestPage' => $guestPage,
    'user' => $user,
    'pageTitle' => $pageTitle,
    'mainContent' => $mainContent,
    'isProjectsTasksPage' => $isProjectsTasksPage
]);

print($layout);
?>
