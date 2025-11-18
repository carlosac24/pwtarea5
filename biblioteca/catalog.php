<?php
require_once __DIR__ . '/includes/header.php';
require_login();

$pdo = getPDO();
$success = '';
$error = '';
$canBorrow = user_has_role('Reader');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
    $bookId = (int) $_POST['book_id'];
    $user = current_user();

    if (!$canBorrow) {
        $error = 'Solo los lectores pueden solicitar préstamos.';
    } else {
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare('SELECT quantity, (
                SELECT COUNT(*) FROM transactions WHERE book_id = books.id AND date_of_return IS NULL
            ) AS loans
            FROM books WHERE id = :id FOR UPDATE');
        $stmt->execute(['id' => $bookId]);
        $book = $stmt->fetch();

        if (!$book) {
            throw new RuntimeException('Libro no encontrado.');
        }

        if ((int) $book['quantity'] <= (int) $book['loans']) {
            throw new RuntimeException('No hay ejemplares disponibles.');
        }

        $stmt = $pdo->prepare('SELECT COUNT(*) FROM transactions WHERE user_id = :user AND book_id = :book AND date_of_return IS NULL');
        $stmt->execute(['user' => $user['id'], 'book' => $bookId]);
        $alreadyLoaned = (int) $stmt->fetchColumn();

        if ($alreadyLoaned > 0) {
            throw new RuntimeException('Ya tienes un préstamo activo de este libro.');
        }

        $stmt = $pdo->prepare('INSERT INTO transactions (user_id, book_id, date_of_issue) VALUES (:user, :book, CURDATE())');
        $stmt->execute(['user' => $user['id'], 'book' => $bookId]);

        $pdo->commit();
        $success = 'Préstamo registrado con éxito.';
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = $e->getMessage();
    }
    }
}

$query = 'SELECT books.*, (
    SELECT COUNT(*) FROM transactions WHERE book_id = books.id AND date_of_return IS NULL
) AS loans FROM books ORDER BY title';
$books = $pdo->query($query)->fetchAll();
?>
<div class="card">
    <h2>Catálogo de libros</h2>
    <p>Explora el catálogo y solicita un préstamo si hay ejemplares disponibles.</p>
    <?php if ($success): ?>
        <div class="alert alert--success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert--error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <table class="table">
        <thead>
            <tr>
                <th>Título</th>
                <th>Autor</th>
                <th>Año</th>
                <th>Género</th>
                <th>Disponibles</th>
                <?php if ($canBorrow): ?>
                    <th>Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($books as $book): ?>
                <?php
                $available = (int) $book['quantity'] - (int) $book['loans'];
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                    <td><?php echo htmlspecialchars((string) $book['year']); ?></td>
                    <td><?php echo htmlspecialchars($book['genre'] ?? ''); ?></td>
                    <td><?php echo max($available, 0); ?></td>
                    <?php if ($canBorrow): ?>
                        <td>
                            <?php if ($available > 0): ?>
                                <form method="POST" action="catalog.php">
                                    <input type="hidden" name="book_id" value="<?php echo (int) $book['id']; ?>">
                                    <button type="submit" class="button">Solicitar</button>
                                </form>
                            <?php else: ?>
                                <span class="badge">No disponible</span>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
