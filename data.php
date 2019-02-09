<?php
const SECONDS_PER_DAY = 86400;

$pageTitle = 'Дела в порядке';
$userName = 'Константин';
$projects = ['Входящие', 'Учеба', 'Работа', 'Домашние дела', 'Авто'];
$tasks = [
    [
        'title' => 'Собеседование в IT компании',
        'implementationDate' => '10.02.2019',
        'projectCategory' => 'Работа',
        'isDone' => false
    ],
    [
        'title' => 'Сделать задание первого раздела',
        'implementationDate' => '21.12.2019',
        'projectCategory' => 'Учеба',
        'isDone' => true
    ],
    [
        'title' => 'Выполнить тестовое задание',
        'implementationDate' => '25.12.2019',
        'projectCategory' => 'Работа',
        'isDone' => false
    ],
    [
        'title' => 'Встреча с другом',
        'implementationDate' => '22.12.2019',
        'projectCategory' => 'Входящие',
        'isDone' => false
    ],
    [
        'title' => 'Купить корм для кота',
        'implementationDate' => 'Нет',
        'projectCategory' => 'Домашние дела',
        'isDone' => false
    ],
    [
        'title' => 'Заказать пиццу',
        'implementationDate' => 'Нет',
        'projectCategory' => 'Домашние дела',
        'isDone' => false
    ]
];
// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);
?>
