-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Авг 09 2018 г., 13:04
-- Версия сервера: 10.0.32-MariaDB-0+deb8u1
-- Версия PHP: 7.1.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `rewesand`
--

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `default_price` int(11) NOT NULL,
  `type_bind_price` int(2) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `title`, `description`, `default_price`, `type_bind_price`) VALUES
(1, 'Форма для школьников', 'Прекрасная форма, вот прям так и хочеться купить.', 10000, 0),
(3, 'Школьная форма для мальчиков', 'Отличная форма для активных и не очень мальчиков. Протирать штаны не протереть. Не рвутся и имеют отталкивающий эффект против травы, шоколада, пиша и окурков', 10000, 1),
(4, 'Школьная форма для девочек', 'Гламурные и няшные вещички для самых модниц.', 5000, 0),
(5, 'Школьная форма для совсем детей', 'Недорого и качественно', 5000, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `product_custom_prices`
--

CREATE TABLE `product_custom_prices` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `start` int(10) NOT NULL,
  `end` int(10) NOT NULL,
  `custom_price` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `product_custom_prices`
--

INSERT INTO `product_custom_prices` (`id`, `product_id`, `start`, `end`, `custom_price`) VALUES
(2, 1, 1451602800, 0, 8000),
(4, 1, 1462053600, 1483225200, 12000),
(5, 1, 1467324000, 1473458400, 15000),
(6, 1, 1496268000, 1508450400, 20000),
(7, 1, 1513292400, 1514674800, 5000),
(8, 3, 1451602800, 0, 12000),
(10, 3, 1483225200, 0, 15000),
(11, 1, 1513724400, 1534716000, 13000);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `product_custom_prices`
--
ALTER TABLE `product_custom_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT для таблицы `product_custom_prices`
--
ALTER TABLE `product_custom_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
