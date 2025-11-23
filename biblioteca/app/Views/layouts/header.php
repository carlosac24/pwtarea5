<?php
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Biblioteca Online</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <script defer src="<?php echo BASE_URL; ?>/assets/js/app.js"></script>
</head>
<body>
<header class="topbar">
    <div class="topbar__brand">Biblioteca Online</div>
    <?php if ($user): ?>
        <nav class="topbar__nav">
            <a href="<?php echo BASE_URL; ?>/home">Inicio</a>
            <a href="<?php echo BASE_URL; ?>/catalog">Catálogo</a>
            <?php if (Auth::hasRole('Administrator', 'Librarian')): ?>
                <a href="<?php echo BASE_URL; ?>/books">Gestión de libros</a>
            <?php endif; ?>
            <?php if (Auth::hasRole('Administrator')): ?>
                <a href="<?php echo BASE_URL; ?>/users">Usuarios</a>
            <?php endif; ?>
            <a href="<?php echo BASE_URL; ?>/transactions">Préstamos</a>
            <span class="topbar__user">Hola, <?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($user['role']); ?>)</span>
            <a href="<?php echo BASE_URL; ?>/logout" style="color: white; text-decoration: underline;">Salir</a>
        </nav>
    <?php endif; ?>
</header>
<main class="layout">
