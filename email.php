<?php
require_once('vendor/autoload.php');
require_once('mysql_helper.php');
require_once('functions.php');

$user['name'] = " ";
$task['name'] = "Отладить отправку почты";
$task['deadline'] = "сегодня";

$subject = "Уведомление от сервиса «Дела в порядке»";


$connection = connect2Database('localhost', 'root', '', 'doingsdone');
$results = checkTasksCloseToDeadline($connection);

if ($results) {
    foreach ($results as $result) {
        $taskName = $result['task'];
        $userName = $result['name'];
        $userEmail = $result['email'];
        $taskDeadline = $result['deadline'];

        $messageBody = "Уважаемый, " . $userName . "!\nУ вас запланирована задача " . $taskName . " на " . $taskDeadline;

        try {
            $transport = (new Swift_SmtpTransport('smtp.mail.ru', 465, 'ssl'))
                ->setUsername('igor_test@list.ru')
                ->setPassword('new_123');

            $message = (new Swift_Message($subject))
                ->setTo($userEmail)
                ->setBody($messageBody)
                ->setFrom('igor_test@list.ru', 'Администратор сервиса');

            $mailer = (new Swift_Mailer($transport))
                ->send($message);
        } catch (Exception $error) {
            echo $error->getMessage();
        }
    }
}
?>
