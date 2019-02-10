CREATE DATABASE doingsdone;

USE doingsdone;

CREATE TABLE projects (
  id int AUTO_INCREMENT PRIMARY KEY,
  name varchar(32) NOT NULL,
  user_id int NOT NULL
)
