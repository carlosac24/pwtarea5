<?php require_once __DIR__ . '/layouts/header.php'; ?>
<div class="card">
    <h2>Panel principal</h2>
    <p>Bienvenido al sistema de biblioteca en línea.</p>
</div>
<div class="grid">
    <div class="card">
        <h3>Usuarios</h3>
        <p><?php echo $stats['total_users']; ?> registrados</p>
    </div>
    <div class="card">
        <h3>Títulos</h3>
        <p><?php echo $stats['total_books']; ?> libros catalogados</p>
    </div>
    <div class="card">
        <h3>Ejemplares disponibles</h3>
        <p><?php echo $stats['available_books']; ?></p>
    </div>
    <div class="card">
        <h3>Préstamos activos</h3>
        <p><?php echo $stats['active_loans']; ?></p>
    </div>
</div>
<?php require_once __DIR__ . '/layouts/footer.php'; ?>
