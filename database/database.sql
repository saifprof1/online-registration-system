CREATE DATABASE online_registration;

USE online_registration;

CREATE TABLE departments (
    department_id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL UNIQUE
);

INSERT INTO departments (department_name)
VALUES
('ICT'),
('CSE'),
('DBA');