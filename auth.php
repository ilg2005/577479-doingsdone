<?php
require_once 'functions.php';
require_once 'init.php';
require_once 'connect.php';

$email = '';
$password = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $email = $email ?? '';
    $password = htmlspecialchars($_POST['password']);
    $password = $password ?? '';


    $requiredFields = [
        'email' => $email,
        'password' => $password
    ];

    $errors = [];
    foreach ($requiredFields as $key => $value) {
        if (empty(trim($value))) {
            $errors[$key] = 'Это поле нужно заполнить';
        }
    }

    if (!isset($errors['email']) && !isEmailValid($email)) {
        $errors['email'] = 'E-mail введен некорректно';
    }

    if (empty($errors)) {
        $userSearch = 'SELECT * FROM users WHERE email = ? LIMIT 1';
        $user = fetchRow($pdo, $userSearch, [$email]);
        if (!$user) {
            $errors['email'] = 'Такой пользователь не найден';
        }
    }

    if (empty($errors)) {
        $isPasswordValid = password_verify($_POST['password'], $user['password']);
        if (!$isPasswordValid) {
            $errors['password'] = 'Неверный пароль';
        } else {
            $_SESSION['user'] = $user;
            header('Location: index.php');
            exit();
        }
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

