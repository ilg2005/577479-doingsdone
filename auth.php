<?php
require_once('mysql_helper.php');
require_once('functions.php');

$guestPage = false;
$connection = connect2Database('localhost', 'root', '', 'doingsdone');
$email = '';
$password = '';
$user = [];
$userName = '';
$projects = [];
$errors = [];

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = $_POST;
    $email = $form['email'] ?? '';
    $password = $form['password'] ?? '';
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

    $email = mysqli_real_escape_string($connection, $form['email']);

    if (!isEmailValid($email)) {
        $errors['email'] = 'E-mail введен некорректно';
    }

    if (empty($errors)) {
        $userSearch = 'SELECT * FROM users WHERE email = ? LIMIT 1';
        $user = fetchRow($connection, $userSearch, [$email]);

        if (!$user) {
            $errors['email'] = 'Такой пользователь не найден';
        } else {
            $isPasswordValid = password_verify($form['password'], $user['password']);
            if ($isPasswordValid) {
                $_SESSION['user'] = $user;
                header('Location: index.php');
                exit();
            } else {
                $errors['password'] = 'Неверный пароль';
            }
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

?>
