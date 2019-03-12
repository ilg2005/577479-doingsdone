<?php
/**
 * Устанавливает соединение с базой данных MySQL
 *
 * @param string $hostName -- строка с именем хоста
 * @param string $userName -- строка с именем пользователя MySQL
 * @param string $pwd -- пароль пользователя MySQL
 * @param string $dbName -- строка с именем базы данных MySQL
 *
 * @return mysqli $link -- объект, представляющий подключение к серверу MySQL
 */
function connect2Database($hostName, $userName, $pwd, $dbName)
{
    $link = mysqli_connect($hostName, $userName, $pwd, $dbName);
    mysqli_set_charset($link, 'utf8');
    checkDatabaseError($link, $link);
    return $link;
}

$connection = connect2Database('localhost', 'root', '', 'doingsdone');

if (!$connection) {
    exit('Произошла ошибка соединения с базой данных!');
}
