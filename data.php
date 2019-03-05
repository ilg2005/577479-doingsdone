<?php

$pageTitle = 'Дела в порядке';
$userName = 'Константин';
$projects = ['Входящие', 'Учеба', 'Работа', 'Домашние дела', 'Авто'];
$tasks = [
    [
        'title' => 'Собеседование в IT компании',
        'deadline' => '10.02.2019',
        'projectCategory' => 'Работа',
        'isDone' => false
    ],
    [
        'title' => 'Сделать задание первого раздела',
        'deadline' => '21.12.2019',
        'projectCategory' => 'Учеба',
        'isDone' => true
    ],
    [
        'title' => 'Выполнить тестовое задание',
        'deadline' => '25.12.2019',
        'projectCategory' => 'Работа',
        'isDone' => false
    ],
    [
        'title' => 'Встреча с другом',
        'deadline' => '22.12.2019',
        'projectCategory' => 'Входящие',
        'isDone' => false
    ],
    [
        'title' => 'Купить корм для кота',
        'deadline' => 'Нет',
        'projectCategory' => 'Домашние дела',
        'isDone' => false
    ],
    [
        'title' => 'Заказать пиццу',
        'deadline' => 'Нет',
        'projectCategory' => 'Домашние дела',
        'isDone' => false
    ]
];
// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

