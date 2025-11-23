<?php
require_once __DIR__ . '/includes/auth.php';

if (session_status() === PHP_SESSION_ACTIVE) {
    $_SESSION = [];
    session_destroy();
}

redirect('login.php');
