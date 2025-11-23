<?php
require_once __DIR__ . '/includes/header.php';
require_login();

$pdo = getPDO();
$user = current_user();
$message = '';
$error = '';
$isStaff = user_has_role('Administrator', 'Librarian');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'issue') {
            if (!$isStaff) {
                throw new RuntimeException('No autorizado.');
            }
            $userId = (int) ($_POST['user_id'] ?? 0);
            $bookId = (int) ($_POST['book_id'] ?? 0);
            if ($userId === 0 || $bookId === 0) {
                throw new RuntimeException('Usuario y libro son obligatorios.');
            }

            $pdo->beginTransaction();
            $stmt = $pdo->prepare('SELECT quantity, (
                SELECT COUNT(*) FROM transactions WHERE book_id = books.id AND date_of_return IS NULL
            ) AS loans FROM books WHERE id = :id FOR UPDATE');
            $stmt->execute(['id' => $bookId]);
            $book = $stmt->fetch();
            if (!$book) {
                throw new RuntimeException('Libro no encontrado.');
            }
            if ((int) $book['quantity'] <= (int) $book['loans']) {
                throw new RuntimeException('No hay ejemplares disponibles.');
            }

            $stmt = $pdo->prepare('INSERT INTO transactions (user_id, book_id, date_of_issue) VALUES (:user_id, :book_id, CURDATE())');
            $stmt->execute([
                'user_id' => $userId,
                'book_id' => $bookId,
            ]);
            $pdo->commit();
            $message = 'Préstamo registrado.';
        } elseif ($action === 'return') {
            $transactionId = (int) ($_POST['transaction_id'] ?? 0);
            if ($transactionId === 0) {
                throw new RuntimeException('Préstamo inválido.');
            }

            $stmt = $pdo->prepare('SELECT * FROM transactions WHERE id = :id');
            $stmt->execute(['id' => $transactionId]);
            $transaction = $stmt->fetch();
            if (!$transaction) {
                throw new RuntimeException('Préstamo no encontrado.');
            }

            if (!$isStaff && (int) $transaction['user_id'] !== (int) $user['id']) {
                throw new RuntimeException('No autorizado.');
            }
            if ($transaction['date_of_return']) {
                throw new RuntimeException('El préstamo ya fue cerrado.');
            }

            $stmt = $pdo->prepare('UPDATE transactions SET date_of_return = CURDATE() WHERE id = :id');
            $stmt->execute(['id' => $transactionId]);
            $message = 'Devolución registrada.';
        }
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = $e->getMessage();
    }
}

$usersList = [];
if ($isStaff) {
    $usersList = $pdo->query('SELECT id, username FROM users ORDER BY username')->fetchAll();
}

$books = $pdo->query('SELECT id, title FROM books ORDER BY title')->fetchAll();

$transactionsQuery = 'SELECT transactions.*, users.username, roles.name AS role_name, books.title FROM transactions
    INNER JOIN users ON transactions.user_id = users.id
    INNER JOIN roles ON users.role_id = roles.id
    INNER JOIN books ON transactions.book_id = books.id';

$params = [];
if (!$isStaff) {
    $transactionsQuery .= ' WHERE users.id = :user_id';
    $params['user_id'] = $user['id'];
}
$transactionsQuery .= ' ORDER BY transactions.date_of_issue DESC, transactions.id DESC';

$stmt = $pdo->prepare($transactionsQuery);
$stmt->execute($params);
$transactions = $stmt->fetchAll();
?>
<div class="card">
    <h2>Préstamos y devoluciones</h2>
    <p>Consulta el historial de préstamos y registra devoluciones.</p>
    <?php if ($message): ?>
        <div class="alert alert--success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert--error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($isStaff): ?>
        <div class="card" style="background-color:#f8f9fb;">
            <h3>Registrar nuevo préstamo</h3>
            <form method="POST" action="transactions.php">
                <input type="hidden" name="action" value="issue">
                <div class="form-group">
                    <label for="user_id">Usuario</label>
                    <select name="user_id" id="user_id" required>
                        <option value="">Seleccionar usuario</option>
                        <?php foreach ($usersList as $u): ?>
                            <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['username']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="book_id">Libro</label>
                    <select name="book_id" id="book_id" required>
                        <option value="">Seleccionar libro</option>
                        <?php foreach ($books as $b): ?>
                            <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['title']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="button">Registrar préstamo</button>
            </form>
        </div>
    <?php endif; ?>
    <table class="table">
        <thead>
            <tr>
                <?php if ($isStaff): ?>
                    <th>Usuario</th>
                    <th>Rol</th>
                <?php endif; ?>
                <th>Libro</th>
                <th>Fecha de préstamo</th>
                <th>Fecha de devolución</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $loan): ?>
                <tr>
                    <?php if ($isStaff): ?>
                        <td><?php echo htmlspecialchars($loan['username']); ?></td>
                        <td><?php echo htmlspecialchars($loan['role_name']); ?></td>
                    <?php endif; ?>
                    <td><?php echo htmlspecialchars($loan['title']); ?></td>
                    <td><?php echo htmlspecialchars($loan['date_of_issue']); ?></td>
                    <td><?php echo $loan['date_of_return'] ? htmlspecialchars($loan['date_of_return']) : '—'; ?></td>
                    <td>
                        <?php if (!$loan['date_of_return'] && ($isStaff || (int) $loan['user_id'] === (int) $user['id'])): ?>
                            <form method="POST" action="transactions.php" data-confirm="Confirmar devolución?">
                                <input type="hidden" name="action" value="return">
                                <input type="hidden" name="transaction_id" value="<?php echo (int) $loan['id']; ?>">
                                <button type="submit" class="button">Registrar devolución</button>
                            </form>
                        <?php else: ?>
                            <span class="badge">Sin acciones</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
