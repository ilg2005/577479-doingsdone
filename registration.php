<?php
require_once 'mysql_helper.php';
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
    $userName = strip_tags($_POST['name']);
    $userName = $userName ?? '';


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

        if (!isEmailValid($email)) {
            $errors['email'] = 'E-mail введен некорректно';
        } else {
            $sql = 'SELECT id FROM users WHERE email = ? LIMIT 1';
            $result = fetchRow($connection, $sql, [$email]);
            if ($result) {
                $errors['email'] = 'Пользователь с таким email уже зарегистрирован';
            }
        }

    if (empty($errors)) {
        $password = password_hash($password, PASSWORD_DEFAULT);

        $addNewUser = 'INSERT INTO users (registration_date, email, name, password) VALUES (NOW(), ?, ?, ?)';
        $stmt = db_get_prepare_stmt($connection, $addNewUser, [$email, $userName, $password]);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: auth.php');
            exit();
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
    'mainContent' => $mainContent
]);

print($layout);

