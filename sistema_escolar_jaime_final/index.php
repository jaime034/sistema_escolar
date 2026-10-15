<?php
require_once __DIR__ . '/config/verifica_sessao.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Sistema Escolar EETEPA</title>
        <link rel="stylesheet" href="assets/css/style.css" />
        <link rel="stylesheet" href="assets/css/rodape.css" />
    </head>
    <body>
        <h1>Sistema Escolar EETEPA</h1>
        <hr />

        <h2>Menu Principal</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="alunos/listar.php">Gerenciar Alunos</a></li>
            <li><a href="disciplinas/listar.php">Gerenciar Disciplinas</a></li>
            <li><a href="matriculas/listar.php">Gerenciar matriculas</a></li>
            <li><a href="matriculas/boletim.php">Gerenciar boletins</a></li>
        </ul>

        <?php include __DIR__ . '/includes/footer.php'; ?>
    </body>
</html>
