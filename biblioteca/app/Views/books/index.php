<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>Gestión de libros</h2>
    <p>Agrega, edita o elimina libros del catálogo.</p>

    <?php if (!empty($message)): ?>
        <div class="alert alert--success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert--error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo BASE_URL; ?>/books" class="form-inline">
        <input type="hidden" name="action" value="create">
        <div class="form-group">
            <input type="text" name="title" placeholder="Título" required>
        </div>
        <div class="form-group">
            <input type="text" name="author" placeholder="Autor" required>
        </div>
        <div class="form-group">
            <input type="number" name="year" placeholder="Año" style="width: 80px;">
        </div>
        <div class="form-group">
            <input type="text" name="genre" placeholder="Género">
        </div>
        <div class="form-group">
            <input type="number" name="quantity" placeholder="Cant." required style="width: 70px;" min="1">
        </div>
        <button type="submit" class="button">Agregar</button>
    </form>
</div>

<div class="card">
    <h3>Listado de libros</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Título</th>
                <th>Autor</th>
                <th>Año</th>
                <th>Género</th>
                <th>Cantidad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($books as $book): ?>
                <tr>
                    <form method="POST" action="<?php echo BASE_URL; ?>/books">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $book['id']; ?>">
                        <td><input type="text" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required></td>
                        <td><input type="text" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required></td>
                        <td><input type="number" name="year" value="<?php echo htmlspecialchars($book['year'] ?? ''); ?>" style="width: 60px;"></td>
                        <td><input type="text" name="genre" value="<?php echo htmlspecialchars($book['genre'] ?? ''); ?>"></td>
                        <td><input type="number" name="quantity" value="<?php echo htmlspecialchars($book['quantity']); ?>" required style="width: 50px;" min="1"></td>
                        <td>
                            <button type="submit" class="button button--small">Guardar</button>
                            <button type="submit" name="action" value="delete" class="button button--small button--danger" onclick="return confirm('¿Eliminar este libro?');">Eliminar</button>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
