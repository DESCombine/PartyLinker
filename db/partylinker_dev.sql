-- phpMyAdmin SQL Dump
-- version 5.2.1deb1ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Dic 24, 2023 alle 09:51
-- Versione del server: 8.0.35-0ubuntu0.23.10.1
-- Versione PHP: 8.2.10-2ubuntu1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `partylinker_dev`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `event_comment`
--

CREATE TABLE `event_comment` (
  `comment_id` int NOT NULL,
  `event_id` int DEFAULT NULL,
  `content` varchar(512) DEFAULT NULL,
  `like` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `event_partecipation`
--

CREATE TABLE `event_partecipation` (
  `event_id` int NOT NULL,
  `partecipant` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `event_photo`
--

CREATE TABLE `event_photo` (
  `photo_id` int NOT NULL,
  `event_id` int NOT NULL,
  `poster` varchar(30) NOT NULL,
  `photo` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `posted` timestamp NOT NULL,
  `like` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `event_post`
--

CREATE TABLE `event_post` (
  `event_id` int NOT NULL,
  `organizer` varchar(30) NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `image` varchar(128) NOT NULL,
  `starting_date` timestamp NOT NULL,
  `ending_date` timestamp NOT NULL,
  `posted` timestamp NOT NULL,
  `like` int NOT NULL,
  `vip` varchar(255) DEFAULT NULL,
  `max_capacity` int NOT NULL,
  `price` float DEFAULT NULL,
  `minimum_age` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `photo_comment`
--

CREATE TABLE `photo_comment` (
  `comment_id` int NOT NULL,
  `photo_id` int DEFAULT NULL,
  `content` varchar(512) DEFAULT NULL,
  `like` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `relationship`
--

CREATE TABLE `relationship` (
  `follows` varchar(30) NOT NULL,
  `followed` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `user`
--

CREATE TABLE `user` (
  `username` varchar(30) NOT NULL,
  `email` varchar(64) NOT NULL,
  `password` varchar(256) NOT NULL,
  `name` varchar(30) NOT NULL,
  `surname` varchar(30) NOT NULL,
  `birth_date` date NOT NULL,
  `photo` varchar(128) DEFAULT NULL,
  `bio` varchar(512) DEFAULT NULL,
  `phone` varchar(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `user`
--

INSERT INTO `user` (`username`, `email`, `password`, `name`, `surname`, `birth_date`, `photo`, `bio`, `phone`) VALUES
('test2', 'test2', '$2y$10$jT1mI4cu5TVZWpmwylH4ZuRjoYbpkkQ3UxGHCxJ8g6GVWq3Jiqe6O', 'test2', 'test2', '2023-12-15', 'test', 'test', 'test');

-- --------------------------------------------------------

--
-- Struttura della tabella `user_settings`
--

CREATE TABLE `user_settings` (
  `username` varchar(30) DEFAULT NULL,
  `language` varchar(2) DEFAULT NULL,
  `notifications` tinyint(1) DEFAULT NULL,
  `2fa` tinyint(1) DEFAULT NULL,
  `organizer` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `event_comment`
--
ALTER TABLE `event_comment`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indici per le tabelle `event_partecipation`
--
ALTER TABLE `event_partecipation`
  ADD KEY `event_id` (`event_id`),
  ADD KEY `partecipant` (`partecipant`);

--
-- Indici per le tabelle `event_photo`
--
ALTER TABLE `event_photo`
  ADD PRIMARY KEY (`photo_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `poster` (`poster`);

--
-- Indici per le tabelle `event_post`
--
ALTER TABLE `event_post`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `organizer` (`organizer`);

--
-- Indici per le tabelle `photo_comment`
--
ALTER TABLE `photo_comment`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `photo_id` (`photo_id`);

--
-- Indici per le tabelle `relationship`
--
ALTER TABLE `relationship`
  ADD KEY `follows` (`follows`),
  ADD KEY `followed` (`followed`);

--
-- Indici per le tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`username`);

--
-- Indici per le tabelle `user_settings`
--
ALTER TABLE `user_settings`
  ADD KEY `username` (`username`);

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `event_comment`
--
ALTER TABLE `event_comment`
  ADD CONSTRAINT `event_comment_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event_post` (`event_id`);

--
-- Limiti per la tabella `event_partecipation`
--
ALTER TABLE `event_partecipation`
  ADD CONSTRAINT `event_partecipation_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event_post` (`event_id`),
  ADD CONSTRAINT `event_partecipation_ibfk_2` FOREIGN KEY (`partecipant`) REFERENCES `user` (`username`);

--
-- Limiti per la tabella `event_photo`
--
ALTER TABLE `event_photo`
  ADD CONSTRAINT `event_photo_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event_post` (`event_id`),
  ADD CONSTRAINT `event_photo_ibfk_2` FOREIGN KEY (`poster`) REFERENCES `user` (`username`);

--
-- Limiti per la tabella `event_post`
--
ALTER TABLE `event_post`
  ADD CONSTRAINT `event_post_ibfk_1` FOREIGN KEY (`organizer`) REFERENCES `user` (`username`);

--
-- Limiti per la tabella `photo_comment`
--
ALTER TABLE `photo_comment`
  ADD CONSTRAINT `photo_comment_ibfk_1` FOREIGN KEY (`photo_id`) REFERENCES `event_photo` (`photo_id`);

--
-- Limiti per la tabella `relationship`
--
ALTER TABLE `relationship`
  ADD CONSTRAINT `relationship_ibfk_1` FOREIGN KEY (`follows`) REFERENCES `user` (`username`),
  ADD CONSTRAINT `relationship_ibfk_2` FOREIGN KEY (`followed`) REFERENCES `user` (`username`);

--
-- Limiti per la tabella `user_settings`
--
ALTER TABLE `user_settings`
  ADD CONSTRAINT `user_settings_ibfk_1` FOREIGN KEY (`username`) REFERENCES `user` (`username`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
