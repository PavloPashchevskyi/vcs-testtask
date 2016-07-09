-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Час створення: Лип 09 2016 р., 19:29
-- Версія сервера: 5.7.12-0ubuntu1.1
-- Версія PHP: 5.6.23-2+deb.sury.org~xenial+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База даних: `sais`
--

-- --------------------------------------------------------

--
-- Структура таблиці `tcoincidences`
--

CREATE TABLE `tcoincidences` (
  `coincidence_id` int(11) NOT NULL,
  `conditionid` int(11) NOT NULL,
  `conclusionid` int(11) NOT NULL,
  `presence` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `tconclusions`
--

CREATE TABLE `tconclusions` (
  `conclusion_id` int(11) NOT NULL,
  `conclusion_name` varchar(255) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `tconditions`
--

CREATE TABLE `tconditions` (
  `condition_id` int(11) NOT NULL,
  `condition_name` varchar(255) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Індекси збережених таблиць
--

--
-- Індекси таблиці `tcoincidences`
--
ALTER TABLE `tcoincidences`
  ADD PRIMARY KEY (`coincidence_id`),
  ADD KEY `conclusionid` (`conclusionid`),
  ADD KEY `conditionid` (`conditionid`);

--
-- Індекси таблиці `tconclusions`
--
ALTER TABLE `tconclusions`
  ADD PRIMARY KEY (`conclusion_id`);

--
-- Індекси таблиці `tconditions`
--
ALTER TABLE `tconditions`
  ADD PRIMARY KEY (`condition_id`);

--
-- Обмеження зовнішнього ключа збережених таблиць
--

--
-- Обмеження зовнішнього ключа таблиці `tcoincidences`
--
ALTER TABLE `tcoincidences`
  ADD CONSTRAINT `tcoincidences_ibfk_1` FOREIGN KEY (`conclusionid`) REFERENCES `tconclusions` (`conclusion_id`),
  ADD CONSTRAINT `tcoincidences_ibfk_2` FOREIGN KEY (`conditionid`) REFERENCES `tconditions` (`condition_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
