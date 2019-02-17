USE doingsdone;

# Добавление названий проектов
INSERT INTO projects (name, user_id)
VALUES ('Входящие', 2),
       ('Учеба', 1),
       ('Работа', 1),
       ('Домашние дела', 3),
       ('Авто', 3),
       ('MySQL из PHP', 4),
       ('Планирование', 4);

# Добавление пользователей
INSERT INTO users
  (email, name, password)
VALUES ('igor@mail.ru', 'Игорь', 'password1'),
       ('olga@mail.ru', 'Ольга', 'password2'),
       ('oleg@mail.ru', 'Олег', 'password3'),
       ('new_user@mail.ru', 'Новый пользователь', 'new_password');

# Добавление задач
INSERT INTO tasks
  (name, deadline, project_id, user_id, is_done)
VALUES ('Собеседование в IT компании', STR_TO_DATE('10.02.2019', '%d.%m.%Y'), 3, 1, 0),
       ('Сделать задание первого раздела', STR_TO_DATE('21.12.2019', '%d.%m.%Y'), 2, 1, 1),
       ('Выполнить тестовое задание', STR_TO_DATE('25.12.2019', '%d.%m.%Y'), 3, 2, 0),
       ('Встреча с другом', STR_TO_DATE('22.12.2019', '%d.%m.%Y'), 1, 2, 0),
       ('Купить корм для кота', NULL, 4, 3, 0),
       ('Заказать пиццу', NULL, 4, 3, 0),
       ('Вывести проект и задачи текущего пользователя', STR_TO_DATE('16.02.2019', '%d.%m.%Y'), 6, 4, 1),
       ('Использовать подготовленные выражения', STR_TO_DATE('16.02.2019', '%d.%m.%Y'), 6, 4, 1),
       ('Составить планы на неделю', STR_TO_DATE('16.02.2019', '%d.%m.%Y'), 7, 4, 0),
       ('Составить планы на месяц', STR_TO_DATE('01.03.2019', '%d.%m.%Y'), 7, 4, 0),
       ('Составить планы на три месяца', STR_TO_DATE('01.04.2019', '%d.%m.%Y'), 7, 4, 0);

# Получить список из всех проектов для одного пользователя
SELECT name
FROM projects
WHERE user_id = 1;

# Получить список из всех задач для одного проекта
SELECT name
FROM tasks
WHERE project_id = 3;

# Пометить задачу как выполненную, и записать дату и время выполнения
UPDATE tasks
SET is_done = 1, implementation_date = CURRENT_TIMESTAMP
WHERE name = 'Собеседование в IT компании';


# Обновить название задачи по ее идентификатору
UPDATE tasks
SET name = 'Заказать вкусную пиццу'
WHERE id = 6;

# Дополнительные запросы:
# Получить список проектов, отсортированный по id пользователя
SELECT name, user_id
FROM projects
ORDER BY user_id ASC;

# Получить список задач, отсортированный по id проекта
SELECT name, project_id
FROM tasks
ORDER BY project_id ASC;

# Показать все задачи вместе с названиями проектов и именами пользователей
SELECT p.id, p.name, t.name, u.name
FROM tasks t
       JOIN projects p
            ON p.id = t.project_id
       JOIN users u
            ON u.id = t.user_id;
