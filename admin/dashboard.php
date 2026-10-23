<?php
require_once '../includes/conexion.php';
require_once '../includes/auth.php';
verificar_admin();

// Obtener estadísticas reales de la base de datos
$stmtP = $pdo->query("SELECT COUNT(*) FROM proyectos");
$total_proyectos = $stmtP->fetchColumn();

$stmtM = $pdo->query("SELECT COUNT(*) FROM mensajes_contacto WHERE leido = 0");
$mensajes_nuevos = $stmtM->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Constructora MC</title>
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
            <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
            <a href="proyectos.php"><i class="fas fa-building"></i> Proyectos</a>
            <a href="mensajes.php"><i class="fas fa-envelope"></i> Mensajes</a>
            <a href="imagenes.php"><i class="fas fa-images"></i> Imágenes</a>
            <a href="configuracion.php"><i class="fas fa-cogs"></i> Configuración</a>
            <a href="cuenta.php"><i class="fas fa-user"></i> Mi Cuenta</a>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="dashboard-header">
            <h2 class="page-title">Resumen General</h2>
        </header>

        <!-- TARJETAS DE ESTADÍSTICAS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon orange"><i class="fas fa-building"></i></div>
                <div class="stat-info">
                    <h4>Total Proyectos</h4>
                    <h2><?= $total_proyectos ?></h2>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-envelope"></i></div>
                <div class="stat-info">
                    <h4>Mensajes Nuevos</h4>
                    <h2><?= $mensajes_nuevos ?></h2>
                </div>
            </div>
        </div>

        <!-- ACCESOS RÁPIDOS -->
        <div class="content-grid">
            <div class="panel-box">
                <div class="panel-title">Accesos Rápidos</div>
                <a href="proyectos.php" class="quick-link">
                    <i class="fas fa-plus"></i> Nuevo Proyecto
                </a>
                <a href="mensajes.php" class="quick-link">
                    <i class="fas fa-envelope"></i> Ver mensajes de contacto
                </a>
                <a href="imagenes.php" class="quick-link">
                    <i class="fas fa-upload"></i> Subir Imagen
                </a>
                <a href="configuracion.php" class="quick-link">
                    <i class="fas fa-edit"></i> Editar Textos
                </a>
            </div>
        </div>
    </main>

</body>
</html>
