<?php
require_once 'vendor/autoload.php';
require_once 'mysql_helper.php';
require_once 'functions.php';

$subject = 'Уведомление от сервиса «Дела в порядке»';

$connection = connect2Database('localhost', 'root', '', 'doingsdone');
$usersWithUrgentTasks = getUsersWithUrgentTasks($connection);

if ($usersWithUrgentTasks) {
    $usersToNotify = [];
    foreach ($usersWithUrgentTasks as $user) {
        $tasks = getUrgentTasks($connection, $user['id']);
        $salutation = 'Уважаемый, ' . $user['user_name'] . '!<br>';
        $notification = 'У вас запланирована задача: ';
        $messageBody = '';
        foreach ($tasks as $task) {
            (count($tasks) === 1 || $task === end($tasks)) ? $delimiter = '' : $delimiter = ', ';
            $messageBody .= '<strong><i>&laquo;' . $task['task_name'] . '&raquo;</i></strong> на <strong>' . date('d.m.Y', strtotime($task['deadline'])) . '</strong>' . $delimiter;
        }
        $user['messageBody'] = $salutation . $notification . $messageBody;
        $usersToNotify[$user['id']] = $user;
    }

    foreach ($usersToNotify as $user) {
        try {
            $transport = (new Swift_SmtpTransport('smtp.mail.ru', 465, 'ssl'))
                ->setUsername('igor_test@list.ru')
                ->setPassword('new_123');

            $message = (new Swift_Message($subject))
                ->setTo($user['email'])
                ->setBody($user['messageBody'], 'text/html')
                ->setFrom('igor_test@list.ru', 'Администратор сервиса');

            $mailer = (new Swift_Mailer($transport))
                ->send($message);
        } catch (Exception $error) {
            echo $error->getMessage();
        }
    }
}

