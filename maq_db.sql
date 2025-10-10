-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 09-Out-2025 às 19:04
-- Versão do servidor: 10.4.6-MariaDB
-- versão do PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `maq_db`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `answers`
--

CREATE TABLE `answers` (
  `id` int(11) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `post_img` varchar(255) DEFAULT NULL,
  `time_created` datetime NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `favorites`
--

CREATE TABLE `favorites` (
  `topic_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `likes`
--

CREATE TABLE `likes` (
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `post_img` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `post_topic`
--

CREATE TABLE `post_topic` (
  `topic_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `topics`
--

CREATE TABLE `topics` (
  `id` int(11) NOT NULL,
  `description` varchar(500) NOT NULL,
  `name` varchar(45) NOT NULL,
  `color` varchar(7) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(320) NOT NULL,
  `phone` varchar(16) DEFAULT NULL,
  `username` varchar(45) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `user_image` varchar(255) DEFAULT NULL,
  `time_created` datetime NOT NULL DEFAULT current_timestamp(),
  `time_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `email`, `phone`, `username`, `password_hash`, `user_image`, `time_created`, `time_updated`) VALUES
(4, 'guilherme@gmail.com', '+55 31 995389129', 'Guilherme', '$2y$10$37Nzcr0da7AsY6W7ZEElKuvUzULQRb4uOy75f604.Cxh/ryMAJ5C6', NULL, '2025-10-07 13:31:44', '2025-10-07 16:31:44'),
(5, 'gujguh@gmail.com', '+55 31 956332987', 'Gustavo', '$2y$10$GtiBjxTsbxab4Mc3IRBXseGuwburuTgTbT02Jlwa5SCt1AC909Yfu', 'http://localhost/maq-api/public/uploads/user_images/pfp_68e5449b07fc64.76152284.png', '2025-10-07 13:47:25', '2025-10-07 16:49:31'),
(6, 'johndoe@gmail.com', '', 'John', '$2y$10$sh4ClRXzezrU4GZV8kwRpOHPx40c7b9RiDlD0qASn50U.WDQQWSrC', NULL, '2025-10-07 14:12:48', '2025-10-07 17:12:48'),
(16, '123@123', '123', '123', '$2y$10$WgoDImlRJQOirwuPLaprQeAxl.gMSTenMTRLnwdNg/IrxI.mlt64e', NULL, '2025-10-09 13:43:33', '2025-10-09 16:43:33'),
(17, 'nietzche@gmail.com', NULL, 'albertCamus@gmail.com', '$2y$10$nvnMRlPSAU1z9DNZOWKQV.zV1qh8.y614h3aoapI7zICVewP0ABwm', NULL, '2025-10-09 13:52:13', '2025-10-09 16:52:13');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Índices para tabela `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`topic_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices para tabela `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`user_id`,`post_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Índices para tabela `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices para tabela `post_topic`
--
ALTER TABLE `post_topic`
  ADD PRIMARY KEY (`topic_id`,`post_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Índices para tabela `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices para tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de tabela `topics`
--
ALTER TABLE `topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `answers_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`);

--
-- Limitadores para a tabela `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`),
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Limitadores para a tabela `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`);

--
-- Limitadores para a tabela `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Limitadores para a tabela `post_topic`
--
ALTER TABLE `post_topic`
  ADD CONSTRAINT `post_topic_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`),
  ADD CONSTRAINT `post_topic_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`);

--
-- Limitadores para a tabela `topics`
--
ALTER TABLE `topics`
  ADD CONSTRAINT `topics_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
