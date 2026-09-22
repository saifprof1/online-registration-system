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

CREATE TABLE batches (
    batch_id INT AUTO_INCREMENT PRIMARY KEY,
    batch_name VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO batches (batch_name) VALUES
('1st Batch'),
('2nd Batch'),
('3rd Batch'),
('4th Batch'),
('5th Batch');

CREATE TABLE years (
    year_id INT AUTO_INCREMENT PRIMARY KEY,
    year_name VARCHAR(20) NOT NULL UNIQUE);

INSERT INTO years (year_name) 
VALUES ('1st Year'),
('2nd Year'),
('3rd Year'),
('4th Year');

CREATE TABLE students (
    student_id VARCHAR(50) PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    last_name VARCHAR(50) NOT NULL,
    father_name VARCHAR(100) NOT NULL,
    mother_name VARCHAR(100) NOT NULL,
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other'),
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    image_path VARCHAR(255),
    address TEXT,
    department_id INT NOT NULL,
    session_id INT NOT NULL,
    batch_id INT NOT NULL,
    year_id INT NOT NULL,
    semester_id INT NOT NULL,

    FOREIGN KEY (department_id)
        REFERENCES departments(department_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    FOREIGN KEY (session_id)
        REFERENCES sessions(session_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    FOREIGN KEY (year_id)
    REFERENCES years(year_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,    

    FOREIGN KEY (semester_id)
        REFERENCES semesters(semester_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    FOREIGN KEY (batch_id)
    REFERENCES batches(batch_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
    );

CREATE TABLE clubs (
    club_id INT AUTO_INCREMENT PRIMARY KEY,
    club_name VARCHAR(100) NOT NULL UNIQUE
);

INSERT INTO clubs (club_name)
 VALUES('Programming Club'),
('Debate Club'),
('Dawah Club'),
('Sports Club'),
('Business Club'),
('Science Club');

CREATE TABLE student_clubs (
    student_id VARCHAR(50) NOT NULL,
    club_id INT NOT NULL,

    PRIMARY KEY (student_id, club_id),

    FOREIGN KEY (student_id)
        REFERENCES students(student_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    FOREIGN KEY (club_id)
        REFERENCES clubs(club_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
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

CREATE TABLE registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    registration_type VARCHAR(50) NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',

    FOREIGN KEY (student_id)
        REFERENCES students(student_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL
);

INSERT INTO admins (username, password, full_name)
VALUES ('admin', 'admin123', 'System Administrator');

