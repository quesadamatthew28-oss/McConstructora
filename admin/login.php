<?php
require_once '../includes/conexion.php';
require_once '../includes/auth.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();
    $correo = filter_var(trim($_POST['correo']), FILTER_VALIDATE_EMAIL);
    $contrasena = $_POST['contrasena'];
    
    if ($correo && !empty($contrasena)) {
        $stmt = $pdo->prepare("SELECT id, nombre, contrasena FROM administradores WHERE correo = ?");
        $stmt->execute([$correo]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($contrasena, $admin['contrasena'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_nombre'] = $admin['nombre'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Credenciales incorrectas.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Constructora MC</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Oswald:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="login-body">

    <div class="login-card">
        <div class="login-left">
            <!-- Aquí va tu logo. He añadido una clase para que no se descontrole de tamaño -->
            <img src="../assets/img/logo-CostructoraMc.png" alt="Constructora MC" class="login-logo">
            <h2>INICIAR SESIÓN</h2>
            
            <?php if($error): ?>
                <p class="login-error"><?= $error ?></p>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="email" name="correo" placeholder="Usuario / Correo" required>
                </div>
                
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="contrasena" placeholder="Contraseña" required>
                </div>
                
                <button type="submit" class="btn-login">LOGIN</button>
                
            </form>
        </div>

        <div class="login-right">
            <div class="wave-effect"></div>
            <img src="../assets/img/logo-CostructoraMc.png" alt="Constructora MC" class="login-right-logo">
            <div class="welcome-text">
                <h1>Welcome.</h1>
                <p>Constructora MC - Panel Administrativo</p>
            </div>
        </div>
    </div>

</body>
</html>