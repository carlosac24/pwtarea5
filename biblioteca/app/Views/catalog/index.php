<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>Catálogo de libros</h2>
    <p>Explora nuestra colección y solicita préstamos.</p>
    <?php if (!empty($message)): ?>
        <div class="alert alert--success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert--error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
</div>

<div class="grid">
    <?php foreach ($books as $book): ?>
        <?php 
            $available = (int) $book['quantity'] - (int) $book['loans'];
            $isAvailable = $available > 0;
        ?>
        <div class="card">
            <h3><?php echo htmlspecialchars($book['title']); ?></h3>
            <p><strong>Autor:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
            <p><strong>Año:</strong> <?php echo htmlspecialchars($book['year'] ?? 'N/A'); ?></p>
            <p><strong>Género:</strong> <?php echo htmlspecialchars($book['genre'] ?? 'N/A'); ?></p>
            <p>
                <strong>Disponibles:</strong> 
                <span class="<?php echo $isAvailable ? 'text-success' : 'text-danger'; ?>">
                    <?php echo $available; ?> / <?php echo $book['quantity']; ?>
                </span>
            </p>
            <?php if ($canBorrow && $isAvailable): ?>
                <form method="POST" action="<?php echo BASE_URL; ?>/catalog">
                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                    <button type="submit" class="button button--block">Solicitar préstamo</button>
                </form>
            <?php elseif (!$isAvailable): ?>
                <button class="button button--block" disabled style="opacity: 0.5; cursor: not-allowed;">No disponible</button>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
