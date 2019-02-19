CREATE DATABASE doingsdone
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE doingsdone;

CREATE TABLE projects
(
  id      int AUTO_INCREMENT PRIMARY KEY,
  name    VARCHAR(128) NOT NULL,
  user_id int          NOT NULL
);

create index projects_name_index
  on projects (name);


CREATE TABLE users
(
  id                int AUTO_INCREMENT PRIMARY KEY,
  registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  email             VARCHAR(128) NOT NULL,
  name              VARCHAR(64)  NOT NULL,
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
  creation_date       TIMESTAMP  DEFAULT CURRENT_TIMESTAMP,
  implementation_date TIMESTAMP,
  is_done             TINYINT(1) DEFAULT 0,
  name                VARCHAR(128) NOT NULL,
  file_name           VARCHAR(32),
  file_path           VARCHAR(128),
  deadline            TIMESTAMP,
  user_id             int          NOT NULL,
  project_id          int          NOT NULL
);

create index tasks_deadline_index
  on tasks (deadline);
create index tasks_file_name_index
  on tasks (file_name);
create index tasks_name_index
  on tasks (name);
create index tasks_is_done_index
  on tasks (is_done);
create index tasks_implementation_date_index
  on tasks (implementation_date);
create index tasks_creation_date_index
  on tasks (creation_date);





