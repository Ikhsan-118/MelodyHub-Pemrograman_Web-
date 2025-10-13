CREATE DATABASE IF NOT EXISTS `melodyhub` 
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `melodyhub`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`username`, `password`) VALUES
('admin', SHA2('123', 256))
ON DUPLICATE KEY UPDATE username=username;

-- tabel tracks (lagu)
CREATE TABLE IF NOT EXISTS `tracks` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `artist` VARCHAR(255) NOT NULL,
  `album` VARCHAR(255) DEFAULT NULL,
  `cover` VARCHAR(255) DEFAULT NULL,     -- path ke cover image (assets/img/...)
  `audio_src` VARCHAR(255) DEFAULT NULL, -- path ke audio (assets/audio/...)
  `duration` VARCHAR(20) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- contoh data tracks sesuai artis yang kamu minta
INSERT INTO `tracks` (`title`, `artist`, `album`, `cover`, `audio_src`, `duration`) VALUES
('Peluru', 'Barasuara', 'Album Peluru', 'assets/img/peluru.jpg', 'assets/audio/peluru.mp3', '4:54'),
('Secerca Cahaya', 'Hindia', 'Single', 'assets/img/secerca-cahaya.jpg', 'assets/audio/secerca-cahaya.mp3', '3:45'),
('Senja yang Rindu', '.feast', 'Single', 'assets/img/senja-rindu.jpg', 'assets/audio/senja-yang-rindu.mp3', '3:30'),
('Spellbound', 'LombaSihir', 'Spellbound EP', 'assets/img/spellbound.jpg', 'assets/audio/spellbound.mp3', '4:00'),
('Echoes', 'The Adams', 'Echoes', 'assets/img/echoes.jpg', 'assets/audio/echoes.mp3', '3:55'),
('Nightfall', 'The Jansen', 'Nightfall', 'assets/img/nightfall.jpg', 'assets/audio/nightfall.mp3', '4:12');
