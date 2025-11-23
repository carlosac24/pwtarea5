<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>Gestión de usuarios</h2>
    <p>Administra los usuarios del sistema.</p>

    <?php if (!empty($message)): ?>
        <div class="alert alert--success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert--error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo BASE_URL; ?>/users" class="form-inline">
        <input type="hidden" name="action" value="create">
        <div class="form-group">
            <input type="text" name="username" placeholder="Nombre de usuario" required>
        </div>
        <div class="form-group">
            <input type="email" name="email" placeholder="Correo electrónico" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Contraseña" required>
        </div>
        <div class="form-group">
            <select name="role_id" required>
                <option value="">Seleccionar rol</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="button">Crear usuario</button>
    </form>
</div>

<div class="card">
    <h3>Listado de usuarios</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Nueva contraseña</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <form method="POST" action="<?php echo BASE_URL; ?>/users">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                        <td><input type="text" name="username" value="<?php echo htmlspecialchars($u['username']); ?>" required></td>
                        <td><input type="email" name="email" value="<?php echo htmlspecialchars($u['email']); ?>" required></td>
                        <td>
                            <select name="role_id" required>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?php echo $role['id']; ?>" <?php echo $u['role_id'] == $role['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($role['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><input type="password" name="password" placeholder="(Sin cambios)"></td>
                        <td>
                            <button type="submit" class="button button--small">Guardar</button>
                            <?php if ($u['id'] != Auth::user()['id']): ?>
                                <button type="submit" name="action" value="delete" class="button button--small button--danger" onclick="return confirm('¿Eliminar usuario?');">Eliminar</button>
                            <?php endif; ?>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
