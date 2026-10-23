<?php
require_once '../includes/conexion.php';
require_once '../includes/auth.php';
require_once '../includes/funciones.php';
verificar_admin();

$mensaje = '';
$error = '';

$stmt = $pdo->prepare("SELECT * FROM administradores WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    $contrasena_actual = $_POST['contrasena_actual'];
    $nuevo_correo = filter_var(trim($_POST['nuevo_correo']), FILTER_VALIDATE_EMAIL);
    $nueva_contrasena = $_POST['nueva_contrasena'];
    $confirmar_contrasena = $_POST['confirmar_contrasena'];

    if (!password_verify($contrasena_actual, $admin['contrasena'])) {
        $error = "La contraseña actual es incorrecta.";
    } else {
        $cambios_realizados = false;
        
        if ($nuevo_correo && $nuevo_correo !== $admin['correo']) {
            $stmtCheck = $pdo->prepare("SELECT id FROM administradores WHERE correo = ? AND id != ?");
            $stmtCheck->execute([$nuevo_correo, $admin['id']]);
            if ($stmtCheck->fetch()) {
                $error = "El nuevo correo ya está en uso por otra cuenta.";
            } else {
                $stmtU = $pdo->prepare("UPDATE administradores SET correo = ? WHERE id = ?");
                if ($stmtU->execute([$nuevo_correo, $admin['id']])) {
                    $cambios_realizados = true;
                }
            }
        }

        if (empty($error) && !empty($nueva_contrasena)) {
            if (strlen($nueva_contrasena) < 6) {
                $error = "La nueva contraseña debe tener al menos 6 caracteres.";
            } elseif ($nueva_contrasena !== $confirmar_contrasena) {
                $error = "Las nuevas contraseñas no coinciden.";
            } else {
                $hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
                $stmtU = $pdo->prepare("UPDATE administradores SET contrasena = ? WHERE id = ?");
                if ($stmtU->execute([$hash, $admin['id']])) {
                    $cambios_realizados = true;
                }
            }
        }

        if ($cambios_realizados && empty($error)) {
            $mensaje = "Cuenta actualizada correctamente.";
            $stmt = $pdo->prepare("SELECT * FROM administradores WHERE id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            $admin = $stmt->fetch();
        } elseif (empty($error)) {
            $mensaje = "No se realizaron cambios.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Oswald:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="dashboard-body">

    <!-- BARRA LATERAL -->
    <aside class="sidebar">
        <div class="brand">CONSTRUCTORA MC</div>
        <img src="../assets/img/logo-CostructoraMc.png" alt="Constructora MC" class="login-logo">
        <nav>
            <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="proyectos.php"><i class="fas fa-building"></i> Proyectos</a>
            <a href="mensajes.php"><i class="fas fa-envelope"></i> Mensajes</a>
            <a href="imagenes.php"><i class="fas fa-images"></i> Imágenes</a>
            <a href="configuracion.php"><i class="fas fa-cogs"></i> Configuración</a>
            <a href="cuenta.php" class="active"><i class="fas fa-user"></i> Mi Cuenta</a>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="dashboard-header">
            <h2 class="page-title">Mi Cuenta</h2>
        </header>

        <?php if($mensaje): ?><p class="alert-success"><?= $mensaje ?></p><?php endif; ?>
        <?php if($error): ?><p class="alert-danger"><?= $error ?></p><?php endif; ?>

        <div class="panel-box" style="max-width: 600px;">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <h3>Datos de Administrador</h3>
                <div class="form-row-full" style="margin-bottom: 2rem;">
                    <label>Correo Actual</label>
                    <input type="text" value="<?= htmlspecialchars($admin['correo']) ?>" disabled style="background: #eee; color: #888;">
                </div>

                <div class="form-row-full">
                    <label>Nuevo Correo (dejar en blanco para no cambiar)</label>
                    <input type="email" name="nuevo_correo" placeholder="ejemplo@correo.com">
                </div>

                <hr style="border: 0; border-top: 1px solid #eee; margin: 2rem 0;">
                
                <h3>Seguridad</h3>
                <div class="form-row-full">
                    <label>Contraseña Actual (Obligatoria para guardar cambios)</label>
                    <input type="password" name="contrasena_actual" required>
                </div>

                <div class="form-row">
                    <div>
                        <label>Nueva Contraseña</label>
                        <input type="password" name="nueva_contrasena">
                    </div>
                    <div>
                        <label>Confirmar Nueva Contraseña</label>
                        <input type="password" name="confirmar_contrasena">
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="margin-top: 1rem;">Actualizar Cuenta</button>
            </form>
        </div>
    </main>
</body>
</html>
