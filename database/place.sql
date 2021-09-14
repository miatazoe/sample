-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2021-07-14 11:45:13
-- サーバのバージョン： 10.4.18-MariaDB
-- PHP のバージョン: 8.0.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `hqsample`
--

--
-- テーブルのデータのダンプ `place`
--

INSERT INTO `place` (`id`, `lpwa_id`, `place_name`, `created_at`, `updated_at`) VALUES
(1, 'AA0002', '千代県庁口本社', '2021-07-12 06:34:07', '2021-07-12 06:34:07'),
(3, 'AA0003', 'テスト地点B', '2021-07-12 06:35:28', '2021-07-12 06:35:28'),
(4, 'BB0004', 'テスト地点C', '2021-07-13 01:27:00', '2021-07-13 01:27:00');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
