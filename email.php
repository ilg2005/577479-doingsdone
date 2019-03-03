<?php
require_once 'vendor/autoload.php';
require_once 'functions.php';

$user['name'] = " ";
$task['name'] = "Отладить отправку почты";
$task['deadline'] = "сегодня";

$subject = "Уведомление от сервиса «Дела в порядке»";
$messageBody = "Уважаемый, " . $user['name'] . "!\nУ вас запланирована задача " . $task['name'] . " на " . $task['deadline'];

try {
// Конфигурация траспорта
    $transport = (new Swift_SmtpTransport('smtp.mail.ru', 465, 'ssl'))
        ->setUsername('igor_test@list.ru')
        ->setPassword('new_123');

// Формирование сообщения
    $message = (new Swift_Message($subject))
        ->setTo('igor_test@list.ru')
        ->setBody($messageBody)
        ->setFrom('igor_test@list.ru', 'Администратор сервиса');

// Отправка сообщения
    $mailer = (new Swift_Mailer($transport))
        ->send($message);
} catch (Exception $error) {
    echo $error->getMessage();
}
?>
