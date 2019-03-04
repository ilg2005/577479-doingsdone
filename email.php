<?php
require_once('vendor/autoload.php');
require_once('mysql_helper.php');
require_once('functions.php');

$subject = "Уведомление от сервиса «Дела в порядке»";

$connection = connect2Database('localhost', 'root', '', 'doingsdone');
$users = getUsersWithUrgentTasks($connection);

if ($users) {
    $usersToNotify = [];
    foreach ($users as $user) {
        $tasks = checkTasksCloseToDeadline($connection, $user['id']);
        $salutation = "Уважаемый, " . $user['user_name'] . "!<br><ul>";
        $messageBody = "";
        foreach ($tasks as $task) {
            $messageBody .= "<li>У вас запланирована задача <strong><i>&laquo;" . $task['task_name'] . "&raquo;</i></strong> на <strong>" . date("d.m.Y", strtotime($task['deadline'])) . "</strong></li>";
        }
        $user['messageBody'] = $salutation . $messageBody . "</ul>";
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
?>
