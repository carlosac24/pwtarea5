<?php
require_once __DIR__ . '/includes/header.php';
require_login();

$pdo = getPDO();

$stats = [
    'total_users' => 0,
    'total_books' => 0,
    'available_books' => 0,
    'active_loans' => 0,
];

try {
    $stats['total_users'] = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    $stats['total_books'] = (int) $pdo->query('SELECT COUNT(*) FROM books')->fetchColumn();
    $stats['available_books'] = (int) $pdo->query('SELECT COALESCE(SUM(quantity),0) FROM books')->fetchColumn();
    $stats['active_loans'] = (int) $pdo->query('SELECT COUNT(*) FROM transactions WHERE date_of_return IS NULL')->fetchColumn();
} catch (PDOException $e) {
    echo '<div class="alert alert--error">No fue posible cargar estadísticas: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
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
<?php require_once __DIR__ . '/includes/footer.php'; ?>
