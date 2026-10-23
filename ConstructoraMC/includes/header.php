<?php
// includes/header.php
// Asegurar que $config esté definido (lo trae index.php)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($config['nombre_empresa'] ?? 'Constructora MC') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,700;0,900;1,900&family=Oswald:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/estilos.css?v=<?= filemtime(__DIR__ . '/../assets/css/estilos.css') ?>">
</head>
<body>

    <header class="header" id="header">
        <div class="container header-container">
            <a href="index.php" class="logo">
                <img src="assets/img/logo-CostructoraMC.png" alt="Constructora MC" class="logo-img">
            </a>

            <nav class="nav" id="nav">
                <ul class="nav-list">
                    <li><a href="#inicio" class="nav-link active">Inicio</a></li>
                    <li><a href="#nosotros" class="nav-link">Nosotros</a></li>
                    <li><a href="#servicios" class="nav-link">Servicios</a></li>
                    <li><a href="#proyectos" class="nav-link">Proyectos</a></li>
                    <li><a href="#contacto" class="nav-link">Contacto</a></li>
                </ul>
            </nav>

            <div class="header-actions">
                <a href="#contacto" class="btn btn-outline">Cotizar Proyecto</a>
                <button class="menu-toggle" id="menu-toggle" aria-label="Abrir menú">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>
