<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$baseUrl = '/sistema-escolar';
$estiloPagina = $estiloPagina ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Sistema Escolar</title>
        <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/style.css" />
        <?php if ($estiloPagina !== null): ?>
            <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/<?= htmlspecialchars($estiloPagina) ?>" />
        <?php endif; ?>
        <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/rodape.css" />
    </head>
    <body class="app-view">
        <header class="cabecalho-sistema">
            <a class="nome-sistema" href="<?= $baseUrl ?>/index.php">
                Sistema Escolar EETEPA
            </a>
            <nav class="navegacao-sistema" aria-label="Navegação principal">
                <a href="<?= $baseUrl ?>/dashboard.php">Dashboard</a>
                <a href="<?= $baseUrl ?>/index.php">Página inicial</a>
                <a href="<?= $baseUrl ?>/alunos/index.php">Alunos</a>
                <a href="<?= $baseUrl ?>/disciplinas/index.php">Disciplinas</a>
                <a href="<?= $baseUrl ?>/matriculas/index.php">Matrículas</a>
            </nav>
            <div class="usuario-sistema">
                Olá,
                <strong><?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'admin') ?></strong>
                |
                <a href="<?= $baseUrl ?>/logout.php">Sair</a>
            </div>
        </header>
