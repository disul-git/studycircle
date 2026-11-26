CREATE DATABASE IF NOT EXISTS studycircle CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE studycircle;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS study_sessions;
DROP TABLE IF EXISTS group_members;
DROP TABLE IF EXISTS study_groups;
DROP TABLE IF EXISTS answers;
DROP TABLE IF EXISTS questions;
DROP TABLE IF EXISTS downloads;
DROP TABLE IF EXISTS resource_comments;
DROP TABLE IF EXISTS resource_ratings;
DROP TABLE IF EXISTS resources;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS subjects;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 profile_picture VARCHAR(255) NULL,
 role ENUM('student','admin') NOT NULL DEFAULT 'student',
 status ENUM('active','blocked') NOT NULL DEFAULT 'active',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_users_role_status(role,status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE subjects (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL UNIQUE,
 description VARCHAR(500) NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categories (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL UNIQUE,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE resources (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 title VARCHAR(180) NOT NULL,
 description TEXT NOT NULL,
 file_path VARCHAR(255) NOT NULL,
 file_type VARCHAR(12) NOT NULL,
 subject_id BIGINT UNSIGNED NOT NULL,
 category_id BIGINT UNSIGNED NOT NULL,
 uploader_id BIGINT UNSIGNED NOT NULL,
 status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
 reject_reason VARCHAR(1000) NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_resources_subject(subject_id),
 INDEX idx_resources_category(category_id),
 INDEX idx_resources_status_created(status,created_at),
 INDEX idx_resources_uploader(uploader_id),
 CONSTRAINT fk_resources_subject FOREIGN KEY(subject_id) REFERENCES subjects(id) ON DELETE RESTRICT,
 CONSTRAINT fk_resources_category FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE RESTRICT,
 CONSTRAINT fk_resources_uploader FOREIGN KEY(uploader_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE resource_ratings (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 resource_id BIGINT UNSIGNED NOT NULL,
 user_id BIGINT UNSIGNED NOT NULL,
 rating TINYINT UNSIGNED NOT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_rating_user_resource(resource_id,user_id),
 CONSTRAINT chk_rating_range CHECK(rating BETWEEN 1 AND 5),
 CONSTRAINT fk_rating_resource FOREIGN KEY(resource_id) REFERENCES resources(id) ON DELETE CASCADE,
 CONSTRAINT fk_rating_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE resource_comments (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 resource_id BIGINT UNSIGNED NOT NULL,
 user_id BIGINT UNSIGNED NOT NULL,
 body TEXT NOT NULL,
 status ENUM('visible','hidden') NOT NULL DEFAULT 'visible',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_comment_resource FOREIGN KEY(resource_id) REFERENCES resources(id) ON DELETE CASCADE,
 CONSTRAINT fk_comment_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_comments_resource_status(resource_id,status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE downloads (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 resource_id BIGINT UNSIGNED NOT NULL,
 user_id BIGINT UNSIGNED NOT NULL,
 downloaded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_download_resource FOREIGN KEY(resource_id) REFERENCES resources(id) ON DELETE CASCADE,
 CONSTRAINT fk_download_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_downloaded_at(downloaded_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE questions (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 subject_id BIGINT UNSIGNED NOT NULL,
 title VARCHAR(180) NOT NULL,
 body TEXT NOT NULL,
 status ENUM('visible','hidden') NOT NULL DEFAULT 'visible',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_question_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 CONSTRAINT fk_question_subject FOREIGN KEY(subject_id) REFERENCES subjects(id) ON DELETE RESTRICT,
 INDEX idx_questions_subject_status(subject_id,status),
 INDEX idx_questions_created(created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE answers (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 question_id BIGINT UNSIGNED NOT NULL,
 user_id BIGINT UNSIGNED NOT NULL,
 body TEXT NOT NULL,
 status ENUM('visible','hidden') NOT NULL DEFAULT 'visible',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_answer_question FOREIGN KEY(question_id) REFERENCES questions(id) ON DELETE CASCADE,
 CONSTRAINT fk_answer_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_answers_question_status(question_id,status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE study_groups (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(150) NOT NULL,
 description TEXT NOT NULL,
 subject_id BIGINT UNSIGNED NOT NULL,
 creator_id BIGINT UNSIGNED NOT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_group_subject FOREIGN KEY(subject_id) REFERENCES subjects(id) ON DELETE RESTRICT,
 CONSTRAINT fk_group_creator FOREIGN KEY(creator_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_groups_subject(subject_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE group_members (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 group_id BIGINT UNSIGNED NOT NULL,
 user_id BIGINT UNSIGNED NOT NULL,
 joined_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_group_member(group_id,user_id),
 CONSTRAINT fk_member_group FOREIGN KEY(group_id) REFERENCES study_groups(id) ON DELETE CASCADE,
 CONSTRAINT fk_member_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE study_sessions (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 group_id BIGINT UNSIGNED NOT NULL,
 title VARCHAR(180) NOT NULL,
 location_or_link VARCHAR(500) NULL,
 start_time DATETIME NOT NULL,
 end_time DATETIME NOT NULL,
 created_by BIGINT UNSIGNED NOT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_session_group FOREIGN KEY(group_id) REFERENCES study_groups(id) ON DELETE CASCADE,
 CONSTRAINT fk_session_creator FOREIGN KEY(created_by) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_sessions_start(start_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE contact_messages (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 email VARCHAR(190) NOT NULL,
 subject VARCHAR(180) NOT NULL,
 message TEXT NOT NULL,
 status ENUM('new','read','closed') NOT NULL DEFAULT 'new',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO subjects(name,description) VALUES
('Algorithms & Data Structures','Core algorithms, data structures, complexity, and problem solving.'),
('Artificial Intelligence','Search, reasoning, machine learning, and AI foundations.'),
('Object Oriented Programming','Classes, objects, inheritance, and software design.'),
('Database Management Systems','Relational databases, SQL, and data modelling.'),
('Web Development','Frontend, backend, and web application development.'),
('Computer Networks','Network protocols, architectures, and communication.');

INSERT INTO categories(name) VALUES
('Lecture Notes'),('Summaries'),('Past Papers'),('Tutorials'),('Presentations'),('Lab Sheets');

-- Register your own account in the app, then promote it to admin using:
-- UPDATE users SET role='admin' WHERE email='your-email@example.com';
