<?php
require_once 'mysql_helper.php';
require_once 'functions.php';
require_once 'init.php';

$email = '';
$password = '';

$connection = connect2Database('localhost', 'root', '', 'doingsdone');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $email = $email ?? '';
    $password = htmlspecialchars($_POST['password']);
    $password = $password ?? '';
    $errors = [];

    $requiredFields = [
        'email' => $email,
        'password' => $password
    ];

    foreach ($requiredFields as $key => $value) {
        if (empty(trim($value))) {
            $errors[$key] = 'Это поле нужно заполнить';
        }
    }

    if (empty($errors) && !isEmailValid($email)) {
        $errors['email'] = 'E-mail введен некорректно';
    }

    if (empty($errors)) {
        $userSearch = 'SELECT * FROM users WHERE email = ? LIMIT 1';
        $user = fetchRow($connection, $userSearch, [$email]);

        if (!$user) {
            $errors['email'] = 'Такой пользователь не найден';
        }
    }

    if (empty($errors)) {
        $isPasswordValid = password_verify($_POST['password'], $user['password']);
        if (!$isPasswordValid) {
            $errors['password'] = 'Неверный пароль';
        }
    }

    if (empty($errors)) {
        $_SESSION['user'] = $user;
        header('Location: index.php');
        exit();
    }
}

$mainContent = includeTemplate('auth.php', [
    'email' => $email,
    'password' => $password,
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

