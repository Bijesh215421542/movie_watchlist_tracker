CREATE DATABASE IF NOT EXISTS `cinetrack_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `cinetrack_db`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('user', 'admin') DEFAULT 'user',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `movies` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `director` VARCHAR(150) NOT NULL,
  `year` INT NOT NULL,
  `genre` VARCHAR(100) NOT NULL,
  `note` TEXT,
  `art` VARCHAR(20) DEFAULT 'p1',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `user_movie_status` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `movie_id` INT NOT NULL,
  `status` ENUM('none', 'watchlist', 'watched', 'favorite') DEFAULT 'none',
  UNIQUE KEY `user_movie_unique` (`user_id`, `movie_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`movie_id`) REFERENCES `movies`(`id`) ON DELETE CASCADE
);

-- Seed default movies
INSERT INTO `movies` (`id`, `title`, `director`, `year`, `genre`, `note`, `art`) VALUES
(1, 'Inception', 'Christopher Nolan', 2010, 'SCI-FI', 'Mind-bending architecture of dreams.', 'p1'),
(2, 'The Godfather', 'Francis Ford Coppola', 1972, 'CRIME', 'An offer I could not refuse.', 'p2'),
(3, 'Parasite', 'Bong Joon-ho', 2019, 'THRILLER', 'Layers within layers.', 'p3'),
(4, 'Blade Runner 2049', 'Denis Villeneuve', 2017, 'SCI-FI', 'Deakins at his peak.', 'p4'),
(5, 'Mulholland Drive', 'David Lynch', 2001, 'MYSTERY', 'Lynch at his most opaque.', 'p5');
