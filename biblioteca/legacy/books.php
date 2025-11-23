<?php
require_once __DIR__ . '/includes/header.php';
require_login();
ensure_role('Administrator', 'Librarian');

$pdo = getPDO();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create') {
            $title = trim($_POST['title'] ?? '');
            $author = trim($_POST['author'] ?? '');
            $year = $_POST['year'] !== '' ? (int) $_POST['year'] : null;
            $genre = trim($_POST['genre'] ?? '');
            $quantity = (int) ($_POST['quantity'] ?? 0);

            if ($title === '' || $author === '' || $quantity <= 0) {
                throw new RuntimeException('Título, autor y cantidad son obligatorios.');
            }

            $stmt = $pdo->prepare('INSERT INTO books (title, author, year, genre, quantity) VALUES (:title, :author, :year, :genre, :quantity)');
            $stmt->execute([
                'title' => $title,
                'author' => $author,
                'year' => $year,
                'genre' => $genre !== '' ? $genre : null,
                'quantity' => $quantity,
            ]);
            $message = 'Libro agregado correctamente.';
        } elseif ($action === 'update') {
            $id = (int) ($_POST['id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $author = trim($_POST['author'] ?? '');
            $year = $_POST['year'] !== '' ? (int) $_POST['year'] : null;
            $genre = trim($_POST['genre'] ?? '');
            $quantity = (int) ($_POST['quantity'] ?? 0);

            if ($id === 0) {
                throw new RuntimeException('Libro inválido.');
            }
            if ($title === '' || $author === '' || $quantity <= 0) {
                throw new RuntimeException('Título, autor y cantidad son obligatorios.');
            }

            $stmt = $pdo->prepare('UPDATE books SET title = :title, author = :author, year = :year, genre = :genre, quantity = :quantity WHERE id = :id');
            $stmt->execute([
                'title' => $title,
                'author' => $author,
                'year' => $year,
                'genre' => $genre !== '' ? $genre : null,
                'quantity' => $quantity,
                'id' => $id,
            ]);
            $message = 'Libro actualizado correctamente.';
        } elseif ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id === 0) {
                throw new RuntimeException('Libro inválido.');
            }
            $stmt = $pdo->prepare('DELETE FROM books WHERE id = :id');
            $stmt->execute(['id' => $id]);
            $message = 'Libro eliminado.';
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$editBook = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM books WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $editBook = $stmt->fetch();
}

$books = $pdo->query('SELECT * FROM books ORDER BY title')->fetchAll();
?>
<div class="card">
    <h2>Gestión de libros</h2>
    <p>Administra el catálogo de libros.</p>
    <?php if ($message): ?>
        <div class="alert alert--success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert--error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <div class="card" style="background-color:#f8f9fb;">
        <h3><?php echo $editBook ? 'Editar libro' : 'Nuevo libro'; ?></h3>
        <form method="POST" action="books.php">
            <input type="hidden" name="action" value="<?php echo $editBook ? 'update' : 'create'; ?>">
            <?php if ($editBook): ?>
                <input type="hidden" name="id" value="<?php echo (int) $editBook['id']; ?>">
            <?php endif; ?>
            <div class="form-group">
                <label for="title">Título</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($editBook['title'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="author">Autor</label>
                <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($editBook['author'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="year">Año</label>
                <input type="number" id="year" name="year" value="<?php echo htmlspecialchars($editBook['year'] ?? ''); ?>" min="0" max="3000">
            </div>
            <div class="form-group">
                <label for="genre">Género</label>
                <input type="text" id="genre" name="genre" value="<?php echo htmlspecialchars($editBook['genre'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="quantity">Cantidad</label>
                <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($editBook['quantity'] ?? ''); ?>" min="1" required>
            </div>
            <button type="submit" class="button"><?php echo $editBook ? 'Actualizar' : 'Agregar'; ?></button>
            <?php if ($editBook): ?>
                <a class="button" style="background-color:#95a5a6;" href="books.php">Cancelar</a>
            <?php endif; ?>
        </form>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Año</th>
                <th>Género</th>
                <th>Ejemplares</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($books as $book): ?>
                <tr>
                    <td><?php echo (int) $book['id']; ?></td>
                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                    <td><?php echo htmlspecialchars((string) $book['year']); ?></td>
                    <td><?php echo htmlspecialchars($book['genre'] ?? ''); ?></td>
                    <td><?php echo (int) $book['quantity']; ?></td>
                    <td class="actions">
                        <a class="button" href="books.php?edit=<?php echo (int) $book['id']; ?>">Editar</a>
                        <form method="POST" action="books.php" data-confirm="¿Eliminar libro?">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo (int) $book['id']; ?>">
                            <button type="submit" class="button button--danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
