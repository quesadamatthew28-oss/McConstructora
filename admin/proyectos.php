<?php
require_once '../includes/conexion.php';
require_once '../includes/auth.php';
require_once '../includes/funciones.php';
verificar_admin();

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    if (isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM proyectos WHERE id = ?");
        if ($stmt->execute([$id])) {
            $mensaje = "Proyecto eliminado correctamente.";
        } else {
            $error = "Error al eliminar el proyecto.";
        }
    }

    if (isset($_POST['accion']) && ($_POST['accion'] === 'crear' || $_POST['accion'] === 'editar')) {
        $titulo = limpiar($_POST['titulo']);
        $descripcion = limpiar($_POST['descripcion']);
        $categoria = limpiar($_POST['categoria']);
        $ubicacion = limpiar($_POST['ubicacion']);
        $estado = $_POST['estado'] === 'publicado' ? 'publicado' : 'borrador';

        if ($_POST['accion'] === 'crear') {
            $stmt = $pdo->prepare("INSERT INTO proyectos (titulo, descripcion, categoria, ubicacion, estado) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$titulo, $descripcion, $categoria, $ubicacion, $estado])) {
                $mensaje = "Proyecto creado correctamente.";
            } else {
                $error = "Error al crear el proyecto.";
            }
        } else {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("UPDATE proyectos SET titulo=?, descripcion=?, categoria=?, ubicacion=?, estado=? WHERE id=?");
            if ($stmt->execute([$titulo, $descripcion, $categoria, $ubicacion, $estado, $id])) {
                $mensaje = "Proyecto actualizado correctamente.";
            } else {
                $error = "Error al actualizar el proyecto.";
            }
        }
    }
}

$stmt = $pdo->query("SELECT * FROM proyectos ORDER BY fecha_creacion DESC");
$proyectos = $stmt->fetchAll();

$editando = null;
if (isset($_GET['editar'])) {
    $stmtE = $pdo->prepare("SELECT * FROM proyectos WHERE id = ?");
    $stmtE->execute([(int)$_GET['editar']]);
    $editando = $stmtE->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyectos - Admin</title>
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
            <a href="proyectos.php" class="active"><i class="fas fa-building"></i> Proyectos</a>
            <a href="mensajes.php"><i class="fas fa-envelope"></i> Mensajes</a>
            <a href="imagenes.php"><i class="fas fa-images"></i> Imágenes</a>
            <a href="configuracion.php"><i class="fas fa-cogs"></i> Configuración</a>
            <a href="cuenta.php"><i class="fas fa-user"></i> Mi Cuenta</a>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="dashboard-header">
            <h2 class="page-title">Gestión de Proyectos</h2>
        </header>

        <?php if($mensaje): ?><p class="alert-success"><?= $mensaje ?></p><?php endif; ?>
        <?php if($error): ?><p class="alert-danger"><?= $error ?></p><?php endif; ?>

        <!-- Formulario -->
        <div class="panel-box" style="margin-bottom: 2rem;">
            <h3><?= $editando ? 'Editar Proyecto' : 'Nuevo Proyecto' ?></h3>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="accion" value="<?= $editando ? 'editar' : 'crear' ?>">
                <?php if($editando): ?><input type="hidden" name="id" value="<?= $editando['id'] ?>"><?php endif; ?>

                <div class="form-row">
                    <div>
                        <label>Título</label>
                        <input type="text" name="titulo" value="<?= $editando ? htmlspecialchars($editando['titulo']) : '' ?>" required>
                    </div>
                    <div>
                        <label>Categoría</label>
                        <input type="text" name="categoria" value="<?= $editando ? htmlspecialchars($editando['categoria']) : '' ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div>
                        <label>Ubicación</label>
                        <input type="text" name="ubicacion" value="<?= $editando ? htmlspecialchars($editando['ubicacion']) : '' ?>" required>
                    </div>
                    <div>
                        <label>Estado</label>
                        <select name="estado">
                            <option value="borrador" <?= $editando && $editando['estado'] === 'borrador' ? 'selected' : '' ?>>Borrador</option>
                            <option value="publicado" <?= $editando && $editando['estado'] === 'publicado' ? 'selected' : '' ?>>Publicado</option>
                        </select>
                    </div>
                </div>

                <div class="form-row-full">
                    <label>Descripción</label>
                    <textarea name="descripcion" rows="4" required><?= $editando ? htmlspecialchars($editando['descripcion']) : '' ?></textarea>
                </div>

                <button type="submit" class="btn-primary"><?= $editando ? 'Actualizar' : 'Guardar' ?></button>
                <?php if($editando): ?><a href="proyectos.php" class="btn-secondary">Cancelar</a><?php endif; ?>
            </form>
        </div>

        <!-- Tabla -->
        <div class="panel-box">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($proyectos as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['titulo']) ?></td>
                        <td><?= htmlspecialchars($p['categoria']) ?></td>
                        <td><span class="badge <?= $p['estado'] ?>"><?= ucfirst($p['estado']) ?></span></td>
                        <td>
                            <a href="proyectos.php?editar=<?= $p['id'] ?>" class="btn-edit">Editar</a> | 
                            <a href="imagenes.php?proyecto_id=<?= $p['id'] ?>" class="btn-edit">Imágenes</a>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar?');">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button type="submit" class="btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
