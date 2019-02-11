USE doingsdone;

# Добавление названий проектов

INSERT INTO projects (name)
VALUES ('Входящие'),
       ('Учеба'),
       ('Работа'),
       ('Домашние дела'),
       ('Авто');

# Добавление пользователей

INSERT INTO users
  (email, name, password)
VALUES ('igor@mail.ru', 'Игорь', 'password1'),
       ('olga@mail.ru', 'Ольга', 'password2'),
       ('oleg@mail.ru', 'Олег', 'password3');

# Добавление задач

INSERT INTO tasks
(creation_date, implementation_date, isDone, name, file_name, file_path, deadline, user_id, project_id)
VALUES ()
