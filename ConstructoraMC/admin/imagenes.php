<?php
require_once '../includes/conexion.php';
require_once '../includes/auth.php';
require_once '../includes/funciones.php';
verificar_admin();

$mensaje = '';
$error = '';

$stmtP = $pdo->query("SELECT id, titulo FROM proyectos ORDER BY titulo ASC");
$proyectos = $stmtP->fetchAll();

$proyecto_seleccionado = isset($_GET['proyecto_id']) ? (int)$_GET['proyecto_id'] : (isset($_POST['proyecto_id']) ? (int)$_POST['proyecto_id'] : 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    if (isset($_POST['accion']) && $_POST['accion'] === 'subir' && $proyecto_seleccionado > 0) {
        if (isset($_FILES['imagen'])) {
            $nombre_archivo = subir_imagen($_FILES['imagen'], '../uploads/');
            if ($nombre_archivo) {
                $es_portada = isset($_POST['es_portada']) ? 1 : 0;
                
                if ($es_portada) {
                    $ruta = 'uploads/' . $nombre_archivo;
                    $stmtU = $pdo->prepare("UPDATE proyectos SET imagen_portada = ? WHERE id = ?");
                    $stmtU->execute([$ruta, $proyecto_seleccionado]);
                }

                $ruta_galeria = 'uploads/' . $nombre_archivo;
                $stmtI = $pdo->prepare("INSERT INTO imagenes_proyectos (proyecto_id, ruta_imagen) VALUES (?, ?)");
                if ($stmtI->execute([$proyecto_seleccionado, $ruta_galeria])) {
                    $mensaje = $es_portada
                        ? "Imagen subida y asignada como portada del proyecto."
                        : "Imagen subida correctamente a la galería.";
                } else {
                    $error = "Error al guardar la imagen en la base de datos.";
                }
            } else {
                $error = "Error al subir la imagen. Verifica el formato y tamaño (máx 700 MB).";
            }
        }
    }

    if (isset($_POST['accion']) && $_POST['accion'] === 'eliminar_img') {
        $id_img = (int)$_POST['id_img'];
        $stmtDel = $pdo->prepare("SELECT ruta_imagen FROM imagenes_proyectos WHERE id = ?");
        $stmtDel->execute([$id_img]);
        $img = $stmtDel->fetch();

        if ($img) {
            $archivo_fisico = '../' . $img['ruta_imagen'];
            if (file_exists($archivo_fisico)) {
                unlink($archivo_fisico);
            }
            $stmtDel2 = $pdo->prepare("DELETE FROM imagenes_proyectos WHERE id = ?");
            $stmtDel2->execute([$id_img]);
            $mensaje = "Imagen eliminada.";
        }
    }

    if (isset($_POST['accion']) && $_POST['accion'] === 'establecer_portada') {
        $id_img = (int)$_POST['id_img'];
        $stmtImg = $pdo->prepare("SELECT ruta_imagen FROM imagenes_proyectos WHERE id = ? AND proyecto_id = ?");
        $stmtImg->execute([$id_img, $proyecto_seleccionado]);
        $imagen = $stmtImg->fetch();

        if ($imagen) {
            $stmtPortada = $pdo->prepare("UPDATE proyectos SET imagen_portada = ? WHERE id = ?");
            if ($stmtPortada->execute([$imagen['ruta_imagen'], $proyecto_seleccionado])) {
                $mensaje = "La portada del proyecto se actualizó correctamente.";
            } else {
                $error = "No se pudo actualizar la portada del proyecto.";
            }
        } else {
            $error = "No se encontró esa imagen para el proyecto seleccionado.";
        }
    }
}

$imagenes = [];
if ($proyecto_seleccionado > 0) {
    $stmtImg = $pdo->prepare("SELECT * FROM imagenes_proyectos WHERE proyecto_id = ? ORDER BY orden ASC");
    $stmtImg->execute([$proyecto_seleccionado]);
    $imagenes = $stmtImg->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imágenes - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Oswald:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        /* Estilo rápido para la galería */
        .img-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 2rem; }
        .img-item { background: #fff; padding: 10px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center; }
        .img-item img { width: 100%; height: 150px; object-fit: cover; border-radius: 8px; margin-bottom: 10px; }
    </style>
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
            <a href="imagenes.php" class="active"><i class="fas fa-images"></i> Imágenes</a>
            <a href="configuracion.php"><i class="fas fa-cogs"></i> Configuración</a>
            <a href="cuenta.php"><i class="fas fa-user"></i> Mi Cuenta</a>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="dashboard-header">
            <h2 class="page-title">Gestión de Imágenes</h2>
        </header>

        <?php if($mensaje): ?><p class="alert-success"><?= $mensaje ?></p><?php endif; ?>
        <?php if($error): ?><p class="alert-danger"><?= $error ?></p><?php endif; ?>

        <div class="panel-box" style="margin-bottom: 2rem;">
            <form method="GET" style="margin-bottom: 1rem;">
                <label>Selecciona un proyecto para gestionar sus fotos:</label>
                <select name="proyecto_id" onchange="this.form.submit()">
                    <option value="0">-- Seleccionar Proyecto --</option>
                    <?php foreach($proyectos as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $proyecto_seleccionado == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['titulo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>

            <?php if($proyecto_seleccionado > 0): ?>
                <h3>Subir nueva imagen al proyecto</h3>
                <form method="POST" enctype="multipart/form-data" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; margin-top: 1rem;">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="accion" value="subir">
                    <input type="hidden" name="proyecto_id" value="<?= $proyecto_seleccionado ?>">
                    <input type="file" name="imagen" accept="image/jpeg, image/png, image/webp" required style="padding: 0.5rem;">
                    <label style="text-transform: none; margin:0; display:flex; align-items:center; gap:5px;">
                        <input type="checkbox" name="es_portada" checked> Usar como portada del proyecto y mostrarla en la página principal
                    </label>
                    <button type="submit" class="btn-primary">Subir Imagen</button>
                </form>

                <hr style="border: 0; border-top: 1px solid #eee; margin: 2rem 0;">

                <div class="img-grid">
                    <?php foreach($imagenes as $img): ?>
                        <div class="img-item">
                            <img src="../<?= htmlspecialchars($img['ruta_imagen']) ?>" alt="Imagen">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="accion" value="establecer_portada">
                                <input type="hidden" name="id_img" value="<?= (int)$img['id'] ?>">
                                <input type="hidden" name="proyecto_id" value="<?= $proyecto_seleccionado ?>">
                                <button type="submit" class="btn-primary">Usar como portada</button>
                            </form>
                            <form method="POST" onsubmit="return confirm('¿Eliminar imagen?');">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="accion" value="eliminar_img">
                                <input type="hidden" name="id_img" value="<?= $img['id'] ?>">
                                <input type="hidden" name="proyecto_id" value="<?= $proyecto_seleccionado ?>">
                                <button type="submit" class="btn-danger">Eliminar</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                    <?php if(empty($imagenes)) echo "<p>No hay imágenes subidas para este proyecto.</p>"; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
