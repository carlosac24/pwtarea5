<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>Préstamos y devoluciones</h2>
    <p>Consulta el historial de préstamos y registra devoluciones.</p>
    <?php if (!empty($message)): ?>
        <div class="alert alert--success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert--error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($isStaff): ?>
        <div class="card" style="background-color:#f8f9fb;">
            <h3>Registrar nuevo préstamo</h3>
            <form method="POST" action="<?php echo BASE_URL; ?>/transactions">
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
                            <form method="POST" action="<?php echo BASE_URL; ?>/transactions" data-confirm="Confirmar devolución?">
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
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
