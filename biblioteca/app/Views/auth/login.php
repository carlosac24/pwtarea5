<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Biblioteca Online</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
<div class="login-container">
    <h1>Biblioteca Online</h1>
    <form method="POST" action="<?php echo BASE_URL; ?>/login">
        <?php if (!empty($error)): ?>
            <div class="alert alert--error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <div class="form-group">
            <label for="email">Correo electrónico</label>
            <input type="email" name="email" id="email" required autofocus>
        </div>
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button type="submit" class="button button--block">Ingresar</button>
    </form>
</div>
</body>
</html>
