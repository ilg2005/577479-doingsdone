<?php
require_once 'vendor/autoload.php';
require_once 'connect.php';
require_once 'functions.php';

$subject = 'Уведомление от сервиса «Дела в порядке»';

$usersWithUrgentTasks = getUsersWithUrgentTasks($pdo);

if ($usersWithUrgentTasks) {
    $users = [];
    foreach ($usersWithUrgentTasks as $key) {
        $users[$key['id']]['tasks'][] = $key['task_name'];
        $users[$key['id']]['email'] = $key['email'];
        $users[$key['id']]['user_name'] = $key['user_name'];
        $users[$key['id']]['deadline'] = $key['deadline'];
    }

    $usersToNotify = [];
    foreach ($users as $user) {
        $salutation = 'Уважаемый, ' . $user['user_name'] . '!<br>';
        $notification = 'У вас запланирована задача: ';
        $messageBody = '';
        $tasks = $user['tasks'];
        foreach ($tasks as $task) {
            $delimiter = (count($tasks) === 1 || $task === end($tasks)) ? '' : ', ';
            $messageBody .= '<strong><i>&laquo;' . $task . '&raquo;</i></strong> на <strong>' . date('d.m.Y', strtotime($user['deadline'])) . '</strong>' . $delimiter;
        }
        $user['messageBody'] = $salutation . $notification . $messageBody;
        $usersToNotify[] = $user;
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
echo '';
