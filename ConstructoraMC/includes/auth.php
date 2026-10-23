<?php
// includes/auth.php
// Control de sesiones y seguridad CSRF

if (session_status() === PHP_SESSION_NONE) {
    // Configuración de sesión segura
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Función para verificar si el admin está logueado (usar en páginas de admin)
function verificar_admin() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

// Función para validar el token CSRF en formularios POST
function validar_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die("Error de seguridad: Token CSRF inválido.");
        }
    }
}
?>