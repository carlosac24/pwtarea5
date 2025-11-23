<?php
require_once __DIR__ . '/includes/header.php';
require_login();
ensure_role('Administrator');

$pdo = getPDO();
$message = '';
$error = '';
$roles = $pdo->query('SELECT * FROM roles ORDER BY id')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $roleId = (int) ($_POST['role_id'] ?? 0);

            if ($username === '' || $email === '' || $password === '' || $roleId === 0) {
                throw new RuntimeException('Todos los campos son obligatorios.');
            }

            $stmt = $pdo->prepare('INSERT INTO users (username, email, password, role_id) VALUES (:username, :email, :password, :role_id)');
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role_id' => $roleId,
            ]);
            $message = 'Usuario creado correctamente.';
        } elseif ($action === 'update') {
            $id = (int) ($_POST['id'] ?? 0);
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $roleId = (int) ($_POST['role_id'] ?? 0);
            $password = $_POST['password'] ?? '';

            if ($id === 0) {
                throw new RuntimeException('Usuario inválido.');
            }
            if ($username === '' || $email === '' || $roleId === 0) {
                throw new RuntimeException('Nombre, correo y rol son obligatorios.');
            }

            $pdo->beginTransaction();
            $stmt = $pdo->prepare('UPDATE users SET username = :username, email = :email, role_id = :role_id WHERE id = :id');
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'role_id' => $roleId,
                'id' => $id,
            ]);

            if ($password !== '') {
                $stmt = $pdo->prepare('UPDATE users SET password = :password WHERE id = :id');
                $stmt->execute([
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'id' => $id,
                ]);
            }
            $pdo->commit();
            $message = 'Usuario actualizado correctamente.';
        } elseif ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id === 0) {
                throw new RuntimeException('Usuario inválido.');
            }
            if ($id === current_user()['id']) {
                throw new RuntimeException('No puedes eliminar tu propia cuenta.');
            }
            $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
            $stmt->execute(['id' => $id]);
            $message = 'Usuario eliminado.';
        }
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = $e->getMessage();
    }
}

$editUser = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $editUser = $stmt->fetch();
}

$users = $pdo->query('SELECT users.*, roles.name AS role_name FROM users INNER JOIN roles ON users.role_id = roles.id ORDER BY users.id DESC')->fetchAll();
?>
<div class="card">
    <h2>Gestión de usuarios</h2>
    <p>Crea, edita y elimina cuentas del sistema.</p>
    <?php if ($message): ?>
        <div class="alert alert--success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert--error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <div class="card" style="background-color:#f8f9fb;">
        <h3><?php echo $editUser ? 'Editar usuario' : 'Nuevo usuario'; ?></h3>
        <form method="POST" action="users.php">
            <input type="hidden" name="action" value="<?php echo $editUser ? 'update' : 'create'; ?>">
            <?php if ($editUser): ?>
                <input type="hidden" name="id" value="<?php echo (int) $editUser['id']; ?>">
            <?php endif; ?>
            <div class="form-group">
                <label for="username">Nombre de usuario</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($editUser['username'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($editUser['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="role_id">Rol</label>
                <select id="role_id" name="role_id" required>
                    <option value="">Selecciona un rol</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo (int) $role['id']; ?>" <?php echo isset($editUser['role_id']) && (int) $editUser['role_id'] === (int) $role['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($role['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Contraseña <?php echo $editUser ? '(dejar en blanco para no cambiar)' : ''; ?></label>
                <input type="password" id="password" name="password" <?php echo $editUser ? '' : 'required'; ?>>
            </div>
            <button type="submit" class="button"><?php echo $editUser ? 'Actualizar' : 'Crear'; ?></button>
            <?php if ($editUser): ?>
                <a class="button" style="background-color:#95a5a6;" href="users.php">Cancelar</a>
            <?php endif; ?>
        </form>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo (int) $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['role_name']); ?></td>
                    <td class="actions">
                        <a class="button" href="users.php?edit=<?php echo (int) $user['id']; ?>">Editar</a>
                        <form method="POST" action="users.php" data-confirm="¿Eliminar usuario?">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo (int) $user['id']; ?>">
                            <button type="submit" class="button button--danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
