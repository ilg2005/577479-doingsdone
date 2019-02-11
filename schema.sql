CREATE DATABASE doingsdone;

USE doingsdone;

CREATE TABLE projects
(
  id      int AUTO_INCREMENT PRIMARY KEY,
  name    varchar(128) NOT NULL,
  user_id int         NOT NULL
);

create index projects_name_index
  on projects (name);


CREATE TABLE users
(
  id                int AUTO_INCREMENT PRIMARY KEY,
  registration_date DATETIME     DEFAULT CURRENT_TIMESTAMP,
  email             VARCHAR(128) NOT NULL,
  name              CHAR         NOT NULL,
  password          VARCHAR(128) NOT NULL
);

create unique index users_email_uindex
  on users (email);
create index users_name_index
  on users (name);
create index users_registration_date_index
  on users (registration_date);


CREATE TABLE tasks
(
  id                  int AUTO_INCREMENT PRIMARY KEY,
  creation_date       DATETIME NOT NULL,
  implementation_date DATETIME NOT NULL,
  isDone              BINARY(1) DEFAULT 0,
  name                CHAR     NOT NULL,
  file_name           CHAR,
  file_path           CHAR,
  deadline            DATETIME NOT NULL,
  user_id             int      NOT NULL,
  project_id          int      NOT NULL
);

create index tasks_deadline_index
  on tasks (deadline);
create index tasks_file_name_index
  on tasks (file_name);
create index tasks_name_index
  on tasks (name);
create index tasks_isDone_index
  on tasks (isDone);
create index tasks_implementation_date_index
  on tasks (implementation_date);
create index tasks_creation_date_index
  on tasks (creation_date);





