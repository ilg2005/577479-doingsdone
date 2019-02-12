USE doingsdone;

# Добавление названий проектов

INSERT INTO projects (name, user_id)
VALUES ('Входящие', 2),
       ('Учеба', 1),
       ('Работа', 1),
       ('Домашние дела', 3),
       ('Авто', 3);

# Добавление пользователей

INSERT INTO users
  (email, name, password)
VALUES ('igor@mail.ru', 'Игорь', 'password1'),
       ('olga@mail.ru', 'Ольга', 'password2'),
       ('oleg@mail.ru', 'Олег', 'password3');

# Добавление задач

INSERT INTO tasks
  (name, deadline, project_id, user_id, isDone)
VALUES ('Собеседование в IT компании', '10.02.2019', 3, 1, 0),
       ('Сделать задание первого раздела', '21.12.2019', 2, 1, 1),
       ('Выполнить тестовое задание', '25.12.2019', 3, 2, 0),
       ('Встреча с другом', '22.12.2019', 1, 2, 0),
       ('Купить корм для кота', '12.02.2019', 4, 3, 0),
       ('Заказать пиццу', '12.02.2019', 4, 3, 0);

# Получить список из всех проектов для одного пользователя

SELECT p.id, p.name
FROM projects p
       JOIN users u
            ON p.user_id = u.id;
