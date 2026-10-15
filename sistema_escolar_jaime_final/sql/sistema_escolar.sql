-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 22/08/2026 às 01:19
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `sistema_escolar`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos`
--

CREATE TABLE `alunos` (
    `id` int(11) NOT NULL,
    `nome` varchar(100) NOT NULL,
    `matricula` varchar(20) NOT NULL,
    `email` varchar(150) NOT NULL,
    `cpf` varchar(14) DEFAULT NULL,
    `data_nascimento` date DEFAULT NULL,
    `ativo` tinyint(1) DEFAULT 1,
    `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
    `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `alunos`
--

INSERT INTO `alunos` (`id`, `nome`, `matricula`, `email`, `data_nascimento`, `ativo`, `criado_em`, `foto`) VALUES
(1, 'Luis Orlando Pinheiro Carvalho', '00159', 'luisorlandocarvalho@gmail.com', '1979-01-19', 1, '2026-08-21 21:47:04', NULL),
(3, 'Jose Amarildo', '195989', 'amarildo@gmail.com', '2018-01-21', 0, '2026-08-21 21:53:33', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `auditoria`
--

CREATE TABLE `auditoria` (
    `id` int(11) NOT NULL,
    `usuario_id` int(11) DEFAULT NULL,
    `acao` varchar(255) NOT NULL,
    `data_hora` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `disciplinas`
--

CREATE TABLE `disciplinas` (
    `id` int(11) NOT NULL,
    `nome` varchar(100) NOT NULL,
    `carga_horaria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `disciplinas`
--

INSERT INTO `disciplinas` (`id`, `nome`, `carga_horaria`) VALUES
(1, 'Matematica', 80),
(2, 'Geografia', 60);

-- --------------------------------------------------------

--
-- Estrutura para tabela `frequencias`
--

CREATE TABLE `frequencias` (
    `id` int(11) NOT NULL,
    `aluno_id` int(11) DEFAULT NULL,
    `disciplina_id` int(11) DEFAULT NULL,
    `data_aula` date DEFAULT NULL,
    `presenca` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `matriculas`
--

CREATE TABLE `matriculas` (
    `aluno_id` int(11) NOT NULL,
    `disciplina_id` int(11) NOT NULL,
    `nota` decimal(4,2) DEFAULT NULL,
    `media` decimal(4,2) DEFAULT NULL,
    `total_aulas` int(11) DEFAULT NULL,
    `presencas` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `matriculas`
--

INSERT INTO `matriculas` (`aluno_id`, `disciplina_id`, `nota`, `media`) VALUES
(1, 1, 10.00, NULL),
(1, 2, 10.00, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
    `id` int(11) NOT NULL,
    `nome` varchar(100) NOT NULL,
    `usuario` varchar(50) DEFAULT NULL,
    `email` varchar(150) DEFAULT NULL,
    `senha_hash` varchar(255) NOT NULL,
    `alterar_senha` tinyint(1) DEFAULT 0,
    `perfil` enum('admin','professor','secretaria') NOT NULL DEFAULT 'secretaria',
    `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `alunos`
--
ALTER TABLE `alunos`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `matricula` (`matricula`);

--
-- Índices de tabela `auditoria`
--
ALTER TABLE `auditoria`
    ADD PRIMARY KEY (`id`),
    ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `disciplinas`
--
ALTER TABLE `disciplinas`
    ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `frequencias`
--
ALTER TABLE `frequencias`
    ADD PRIMARY KEY (`id`),
    ADD KEY `aluno_id` (`aluno_id`),
    ADD KEY `disciplina_id` (`disciplina_id`);

--
-- Índices de tabela `matriculas`
--
ALTER TABLE `matriculas`
    ADD PRIMARY KEY (`aluno_id`,`disciplina_id`),
    ADD KEY `disciplina_id` (`disciplina_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `email` (`email`),
    ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `alunos`
--
ALTER TABLE `alunos`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `auditoria`
--
ALTER TABLE `auditoria`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `disciplinas`
--
ALTER TABLE `disciplinas`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `frequencias`
--
ALTER TABLE `frequencias`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `auditoria`
--
ALTER TABLE `auditoria`
    ADD CONSTRAINT `auditoria_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `frequencias`
--
ALTER TABLE `frequencias`
    ADD CONSTRAINT `frequencias_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`),
    ADD CONSTRAINT `frequencias_ibfk_2` FOREIGN KEY (`disciplina_id`) REFERENCES `disciplinas` (`id`);

--
-- Restrições para tabelas `matriculas`
--
ALTER TABLE `matriculas`
    ADD CONSTRAINT `matriculas_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE,
    ADD CONSTRAINT `matriculas_ibfk_2` FOREIGN KEY (`disciplina_id`) REFERENCES `disciplinas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
