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

CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    father_name VARCHAR(100) NOT NULL,
    mother_name VARCHAR(100) NOT NULL,
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other'),
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    address TEXT,
    department_id INT NOT NULL,
    session_id INT NOT NULL,
    semester_id INT NOT NULL,

    FOREIGN KEY (department_id)
        REFERENCES departments(department_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    FOREIGN KEY (session_id)
        REFERENCES sessions(session_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    FOREIGN KEY (semester_id)
        REFERENCES semesters(semester_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

CREATE TABLE sessions (
    session_id INT AUTO_INCREMENT PRIMARY KEY,
    session_name VARCHAR(20) NOT NULL UNIQUE
);

INSERT INTO sessions (session_name)
VALUES
('2021-22'),
('2022-23'),
('2023-24'),
('2024-25'),
('2025-26'),
('2026-27');

CREATE TABLE semesters (
    semester_id INT AUTO_INCREMENT PRIMARY KEY,
    semester_name VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO semesters (semester_name)
VALUES
('1st Semester'),
('2nd Semester'),
('3rd Semester'),
('4th Semester'),
('5th Semester'),
('6th Semester'),
('7th Semester'),
('8th Semester');