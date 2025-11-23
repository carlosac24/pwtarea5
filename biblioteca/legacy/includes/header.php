<?php
// header.php - common HTML head and navigation
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/auth.php';
$user = current_user();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Biblioteca Online</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script defer src="assets/js/app.js"></script>
</head>
<body>
<header class="topbar">
    <div class="topbar__brand">Biblioteca Online</div>
    <?php if ($user): ?>
        <nav class="topbar__nav">
            <a href="dashboard.php">Inicio</a>
            <a href="catalog.php">Catálogo</a>
            <?php if (user_has_role('Administrator', 'Librarian')): ?>
                <a href="books.php">Gestión de libros</a>
            <?php endif; ?>
            <?php if (user_has_role('Administrator')): ?>
                <a href="users.php">Usuarios</a>
            <?php endif; ?>
            <a href="transactions.php">Préstamos</a>
            <span class="topbar__user">Hola, <?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($user['role']); ?>)</span>
            <form class="topbar__logout" method="POST" action="logout.php">
                <button type="submit">Salir</button>
            </form>
        </nav>
    <?php endif; ?>
</header>
<main class="layout">
