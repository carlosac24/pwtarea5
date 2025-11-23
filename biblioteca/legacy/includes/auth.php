<?php
// auth.php - helpers to manage authentication and authorization state

require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login(): void
{
    if (!isset($_SESSION['user'])) {
        redirect('login.php');
    }
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function user_has_role(string ...$roles): bool
{
    $user = current_user();
    if (!$user) {
        return false;
    }

    return in_array($user['role'], $roles, true);
}

function ensure_role(string ...$roles): void
{
    if (!user_has_role(...$roles)) {
        http_response_code(403);
        echo '<h1>403 Acceso denegado</h1>';
        echo '<p>No cuentas con permisos suficientes para ver esta página.</p>';
        echo '<p><a href="dashboard.php">Regresar al panel</a></p>';
        exit;
    }
}
