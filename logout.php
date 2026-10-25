<?php
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_ACTIVE) {
    $_SESSION = [];
    session_destroy();
}

header('Location: ' . BASE_URL . '/');
exit;
