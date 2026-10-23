<?php
require_once '../includes/conexion.php';
require_once '../includes/auth.php';
verificar_admin();

$mensaje_estado = '';
$error_estado = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    if (($_POST['accion'] ?? '') === 'marcar_leido') {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id && $id > 0) {
            $stmt = $pdo->prepare('UPDATE mensajes_contacto SET leido = 1 WHERE id = ?');
            $stmt->execute([$id]);
            $mensaje_estado = 'El mensaje se marcó como leído.';
        } else {
            $error_estado = 'No se pudo identificar el mensaje.';
        }
    }
}

$filtro = ($_GET['filtro'] ?? 'todos') === 'nuevos' ? 'nuevos' : 'todos';
$consulta = 'SELECT * FROM mensajes_contacto';
if ($filtro === 'nuevos') {
    $consulta .= ' WHERE leido = 0';
}
$consulta .= ' ORDER BY id DESC';
$mensajes = $pdo->query($consulta)->fetchAll();

function escapar($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes - Constructora MC</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Oswald:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="dashboard-body">
    <aside class="sidebar">
        <div class="brand">CONSTRUCTORA MC</div>
        <img src="../assets/img/logo-CostructoraMc.png" alt="Constructora MC" class="login-logo">
        <nav>
            <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="proyectos.php"><i class="fas fa-building"></i> Proyectos</a>
            <a href="mensajes.php" class="active"><i class="fas fa-envelope"></i> Mensajes</a>
            <a href="imagenes.php"><i class="fas fa-images"></i> Imágenes</a>
            <a href="configuracion.php"><i class="fas fa-cogs"></i> Configuración</a>
            <a href="cuenta.php"><i class="fas fa-user"></i> Mi Cuenta</a>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="dashboard-header">
            <h2 class="page-title">Mensajes de contacto</h2>
        </header>

        <?php if ($mensaje_estado): ?><p class="alert-success"><?= escapar($mensaje_estado) ?></p><?php endif; ?>
        <?php if ($error_estado): ?><p class="alert-danger"><?= escapar($error_estado) ?></p><?php endif; ?>

        <div class="message-filters" aria-label="Filtrar mensajes">
            <a href="mensajes.php?filtro=todos" class="<?= $filtro === 'todos' ? 'active' : '' ?>">Todos</a>
            <a href="mensajes.php?filtro=nuevos" class="<?= $filtro === 'nuevos' ? 'active' : '' ?>">No leídos</a>
        </div>

        <?php if (!$mensajes): ?>
            <section class="panel-box messages-empty">
                <i class="far fa-envelope-open" aria-hidden="true"></i>
                <h3>No hay mensajes<?= $filtro === 'nuevos' ? ' nuevos' : '' ?></h3>
                <p>Cuando alguien envíe el formulario de contacto, aparecerá aquí.</p>
            </section>
        <?php else: ?>
            <div class="messages-list">
                <?php foreach ($mensajes as $item): ?>
                    <article class="message-card <?= empty($item['leido']) ? 'unread' : '' ?>">
                        <div class="message-heading">
                            <div>
                                <h3><?= escapar($item['nombre'] ?? 'Sin nombre') ?></h3>
                                <a href="mailto:<?= escapar($item['correo'] ?? '') ?>"><?= escapar($item['correo'] ?? '') ?></a>
                            </div>
                            <span class="badge <?= empty($item['leido']) ? 'message-unread' : 'message-read' ?>">
                                <?= empty($item['leido']) ? 'No leído' : 'Leído' ?>
                            </span>
                        </div>
                        <?php if (!empty($item['telefono'])): ?>
                            <p class="message-phone"><i class="fas fa-phone" aria-hidden="true"></i> <a href="tel:<?= escapar($item['telefono']) ?>"><?= escapar($item['telefono']) ?></a></p>
                        <?php endif; ?>
                        <p class="message-body"><?= nl2br(escapar($item['mensaje'] ?? '')) ?></p>
                        <?php if (!empty($item['fecha_creacion'])): ?>
                            <p class="message-date">Recibido: <?= escapar($item['fecha_creacion']) ?></p>
                        <?php endif; ?>
                        <?php if (empty($item['leido'])): ?>
                            <form method="POST" class="message-actions">
                                <input type="hidden" name="csrf_token" value="<?= escapar($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="accion" value="marcar_leido">
                                <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                <button type="submit" class="btn-primary"><i class="fas fa-check" aria-hidden="true"></i> Marcar como leído</button>
                            </form>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
