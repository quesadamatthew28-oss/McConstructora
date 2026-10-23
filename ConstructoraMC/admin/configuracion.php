<?php
require_once '../includes/conexion.php';
require_once '../includes/auth.php';
require_once '../includes/funciones.php';
verificar_admin();

$mensaje = '';
$error = '';

// Agrega el campo para la imagen de Nosotros en instalaciones existentes.
$columnaNosotros = $pdo->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'configuracion_empresa' AND COLUMN_NAME = 'nosotros_imagen'")->fetchColumn();
if ((int)$columnaNosotros === 0) {
    $pdo->exec("ALTER TABLE configuracion_empresa ADD nosotros_imagen VARCHAR(255) NULL");
}

$stmt = $pdo->query("SELECT * FROM configuracion_empresa LIMIT 1");
$config = $stmt->fetch();
$iconosServicio = [
    'fa-building' => 'Edificios',
    'fa-house' => 'Casas',
    'fa-house-chimney' => 'Viviendas',
    'fa-city' => 'Urbanismo',
    'fa-warehouse' => 'Bodegas',
    'fa-hotel' => 'Hoteles',
    'fa-store' => 'Locales comerciales',
    'fa-landmark' => 'Instituciones',
    'fa-trowel-bricks' => 'Albañilería',
    'fa-hammer' => 'Construcción',
    'fa-screwdriver-wrench' => 'Reparaciones',
    'fa-wrench' => 'Mantenimiento',
    'fa-toolbox' => 'Herramientas',
    'fa-gears' => 'Ingeniería',
    'fa-helmet-safety' => 'Seguridad en obra',
    'fa-person-digging' => 'Excavaciones',
    'fa-truck' => 'Transporte',
    'fa-truck-pickup' => 'Maquinaria',
    'fa-road' => 'Carreteras',
    'fa-bridge' => 'Puentes',
    'fa-drafting-compass' => 'Diseño arquitectónico',
    'fa-compass-drafting' => 'Planos',
    'fa-ruler-combined' => 'Mediciones',
    'fa-ruler-horizontal' => 'Topografía',
    'fa-paint-roller' => 'Pintura',
    'fa-brush' => 'Acabados',
    'fa-plug' => 'Instalaciones eléctricas',
    'fa-bolt' => 'Electricidad',
    'fa-lightbulb' => 'Iluminación',
    'fa-faucet' => 'Fontanería',
    'fa-water' => 'Agua y drenajes',
    'fa-shower' => 'Baños',
    'fa-bath' => 'Remodelación de baños',
    'fa-door-open' => 'Puertas',
    'fa-window-maximize' => 'Ventanas',
    'fa-tree' => 'Paisajismo',
    'fa-seedling' => 'Jardinería',
    'fa-leaf' => 'Sostenibilidad',
    'fa-solar-panel' => 'Paneles solares',
    'fa-sun' => 'Energía solar',
    'fa-clipboard-check' => 'Inspección de obra',
    'fa-list-check' => 'Control de calidad',
    'fa-file-contract' => 'Contratos',
    'fa-pen-ruler' => 'Planificación',
    'fa-calculator' => 'Presupuestos',
    'fa-chart-line' => 'Gestión de proyectos',
    'fa-check' => 'Entrega de proyectos',
    'fa-shield-halved' => 'Protección',
    'fa-boxes-stacked' => 'Materiales',
    'fa-cubes' => 'Estructuras',
    'fa-layer-group' => 'Acabados y capas',
    'fa-pallet' => 'Suministros',
    'fa-fire-extinguisher' => 'Prevención de incendios',
    'fa-recycle' => 'Reciclaje',
];
$stmtServicios = $pdo->query("SELECT * FROM servicios ORDER BY orden ASC, id ASC");
$serviciosAdmin = $stmtServicios->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    if (($_POST['accion'] ?? '') === 'guardar_servicios') {
        try {
            $pdo->beginTransaction();

            $ids = $_POST['servicio_id'] ?? [];
            $titulos = $_POST['servicio_titulo'] ?? [];
            $descripciones = $_POST['servicio_descripcion'] ?? [];
            $iconos = $_POST['servicio_icono'] ?? [];
            $ordenes = $_POST['servicio_orden'] ?? [];
            $eliminar = $_POST['eliminar_servicio'] ?? [];

            foreach ($ids as $indice => $idServicio) {
                $idServicio = (int)$idServicio;
                if ($idServicio < 1) {
                    continue;
                }

                if (isset($eliminar[$idServicio])) {
                    $stmtEliminar = $pdo->prepare("DELETE FROM servicios WHERE id = ?");
                    $stmtEliminar->execute([$idServicio]);
                    continue;
                }

                $tituloServicio = trim((string)($titulos[$indice] ?? ''));
                $descripcionServicio = trim((string)($descripciones[$indice] ?? ''));
                $iconoServicio = preg_replace('/[^a-zA-Z0-9_ -]/', '', trim((string)($iconos[$indice] ?? 'fa-building')));
                $ordenServicio = (int)($ordenes[$indice] ?? 0);

                if ($tituloServicio === '') {
                    throw new RuntimeException('Cada servicio debe tener un título o marcarse para eliminar.');
                }

                $stmtActualizar = $pdo->prepare("UPDATE servicios SET titulo = ?, descripcion = ?, icono = ?, orden = ? WHERE id = ?");
                $stmtActualizar->execute([$tituloServicio, $descripcionServicio, $iconoServicio, $ordenServicio, $idServicio]);
            }

            $nuevoTitulo = trim((string)($_POST['nuevo_servicio_titulo'] ?? ''));
            $nuevaDescripcion = trim((string)($_POST['nuevo_servicio_descripcion'] ?? ''));
            if ($nuevoTitulo !== '') {
                $nuevoIcono = preg_replace('/[^a-zA-Z0-9_ -]/', '', trim((string)($_POST['nuevo_servicio_icono'] ?? 'fa-building')));
                $nuevoOrden = (int)($_POST['nuevo_servicio_orden'] ?? 0);
                $stmtNuevo = $pdo->prepare("INSERT INTO servicios (titulo, descripcion, icono, orden) VALUES (?, ?, ?, ?)");
                $stmtNuevo->execute([$nuevoTitulo, $nuevaDescripcion, $nuevoIcono, $nuevoOrden]);
            }

            $pdo->commit();
            $mensaje = 'Servicios actualizados correctamente.';
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = $e instanceof RuntimeException
                ? $e->getMessage()
                : 'No se pudieron guardar los servicios. Verifica los datos e inténtalo de nuevo.';
        }

        $stmtServicios = $pdo->query("SELECT * FROM servicios ORDER BY orden ASC, id ASC");
        $serviciosAdmin = $stmtServicios->fetchAll();
    } else {

    $nuevas_imagenes = ['nosotros_imagen' => null, 'hero_imagen' => null];
    $campos_imagen = [
        'imagen_nosotros' => 'nosotros_imagen',
        'imagen_hero' => 'hero_imagen',
    ];
    foreach ($campos_imagen as $campo_archivo => $campo_config) {
        if (!isset($_FILES[$campo_archivo]) || $_FILES[$campo_archivo]['error'] === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        if ($_FILES[$campo_archivo]['error'] !== UPLOAD_ERR_OK) {
            $error = 'PHP no pudo recibir una de las imágenes. Revisa el tamaño máximo de subida configurado en XAMPP.';
            continue;
        }

        $nombre_imagen = subir_imagen($_FILES[$campo_archivo], '../uploads/');
        if ($nombre_imagen) {
            $nuevas_imagenes[$campo_config] = 'uploads/' . $nombre_imagen;
        } else {
            $error = 'No se pudo subir una imagen. Usa JPG, PNG o WEBP y verifica que no supere 700 MB.';
        }
    }

    $nombre_empresa = limpiar($_POST['nombre_empresa']);
    $descripcion_corta = limpiar($_POST['descripcion_corta']);
    $texto_nosotros = limpiar($_POST['texto_nosotros']);
    $telefono = limpiar($_POST['telefono']);
    $whatsapp = limpiar($_POST['whatsapp']);
    $correo_contacto = filter_var(trim($_POST['correo_contacto']), FILTER_VALIDATE_EMAIL);
    $direccion = limpiar($_POST['direccion']);
    $facebook = limpiar($_POST['facebook']);
    $instagram = limpiar($_POST['instagram']);
    $hero_titulo = limpiar($_POST['hero_titulo']);
    $hero_subtitulo = limpiar($_POST['hero_subtitulo']);
    
    $stat_1_numero = limpiar($_POST['stat_1_numero']);
    $stat_1_texto = limpiar($_POST['stat_1_texto']);
    $stat_2_numero = limpiar($_POST['stat_2_numero']);
    $stat_2_texto = limpiar($_POST['stat_2_texto']);
    $stat_3_numero = limpiar($_POST['stat_3_numero']);
    $stat_3_texto = limpiar($_POST['stat_3_texto']);
    $stat_4_numero = limpiar($_POST['stat_4_numero']);
    $stat_4_texto = limpiar($_POST['stat_4_texto']);

    $stmtU = $pdo->prepare("UPDATE configuracion_empresa SET 
        nombre_empresa=?, descripcion_corta=?, texto_nosotros=?, telefono=?, whatsapp=?, correo_contacto=?, 
        direccion=?, facebook=?, instagram=?, hero_titulo=?, hero_subtitulo=?, 
        stat_1_numero=?, stat_1_texto=?, stat_2_numero=?, stat_2_texto=?, 
        stat_3_numero=?, stat_3_texto=?, stat_4_numero=?, stat_4_texto=? 
        WHERE id=?");

    if ($stmtU->execute([
        $nombre_empresa, $descripcion_corta, $texto_nosotros, $telefono, $whatsapp, $correo_contacto,
        $direccion, $facebook, $instagram, $hero_titulo, $hero_subtitulo,
        $stat_1_numero, $stat_1_texto, $stat_2_numero, $stat_2_texto,
        $stat_3_numero, $stat_3_texto, $stat_4_numero, $stat_4_texto, $config['id']
    ])) {
        foreach ($nuevas_imagenes as $campo_config => $ruta_imagen) {
            if ($ruta_imagen !== null) {
                $stmtImagen = $pdo->prepare("UPDATE configuracion_empresa SET {$campo_config} = ? WHERE id = ?");
                $stmtImagen->execute([$ruta_imagen, $config['id']]);
            }
        }

        $stmtCheck = $pdo->prepare("SELECT telefono, hero_titulo FROM configuracion_empresa WHERE id = ?");
        $stmtCheck->execute([$config['id']]);
        $check = $stmtCheck->fetch();
        
        if ($check['telefono'] === $telefono) {
            if ($error === '') {
                $imagenes_actualizadas = array_filter($nuevas_imagenes, function ($ruta) {
                    return $ruta !== null;
                });
                $mensaje = $imagenes_actualizadas
                    ? 'Configuración e imagen(es) actualizada(s) correctamente.'
                    : 'Configuración actualizada y verificada correctamente.';
            }
            $stmt = $pdo->query("SELECT * FROM configuracion_empresa LIMIT 1");
            $config = $stmt->fetch();
        } else {
            $error = "Error de persistencia en la base de datos.";
        }
    } else {
        $error = "Error al ejecutar la consulta SQL.";
    }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - Admin</title>
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
            <a href="configuracion.php" class="active"><i class="fas fa-cogs"></i> Configuración</a>
            <a href="cuenta.php"><i class="fas fa-user"></i> Mi Cuenta</a>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="dashboard-header">
            <h2 class="page-title">Configuración de la Empresa</h2>
        </header>

        <?php if($mensaje): ?><p class="alert-success"><?= $mensaje ?></p><?php endif; ?>
        <?php if($error): ?><p class="alert-danger"><?= $error ?></p><?php endif; ?>

        <div class="panel-box">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <h3>Información General</h3>
                <div class="form-row">
                    <div>
                        <label>Nombre Empresa</label>
                        <input type="text" name="nombre_empresa" value="<?= htmlspecialchars($config['nombre_empresa']) ?>" required>
                    </div>
                    <div>
                        <label>Correo de Contacto</label>
                        <input type="email" name="correo_contacto" value="<?= htmlspecialchars($config['correo_contacto']) ?>" required>
                    </div>
                </div>
                <div class="form-row-full">
                    <label>Descripción Corta (Footer)</label>
                    <textarea name="descripcion_corta" rows="2"><?= htmlspecialchars($config['descripcion_corta']) ?></textarea>
                </div>
                <div class="form-row-full">
                    <label>Texto Nosotros</label>
                    <textarea name="texto_nosotros" rows="4"><?= htmlspecialchars($config['texto_nosotros']) ?></textarea>
                </div>
                <div class="form-row-full">
                    <label>Foto de la sección Nosotros</label>
                    <?php
                    $ruta_imagen_nosotros = $config['nosotros_imagen'] ?? '';
                    $archivo_imagen_nosotros = __DIR__ . '/../' . ltrim($ruta_imagen_nosotros, '/');
                    $imagen_nosotros_actual = $ruta_imagen_nosotros !== '' && file_exists($archivo_imagen_nosotros)
                        ? '../' . ltrim($ruta_imagen_nosotros, '/')
                        : '../assets/img/logo-CostructoraMC.png';
                    ?>
                    <img src="<?= htmlspecialchars($imagen_nosotros_actual) ?>" alt="Foto actual de Nosotros" style="width: min(100%, 320px); max-height: 180px; object-fit: contain; margin: 0.5rem 0 1rem;">
                    <input type="file" name="imagen_nosotros" accept="image/jpeg,image/png,image/webp">
                    <small>Formatos permitidos: JPG, PNG y WEBP. Si no seleccionas una imagen, se conserva la actual.</small>
                </div>

                <h3 style="margin-top: 2rem;">Contacto y Redes</h3>
                <div class="form-row">
                    <div>
                        <label>Teléfono</label>
                        <input type="text" name="telefono" value="<?= htmlspecialchars($config['telefono']) ?>">
                    </div>
                    <div>
                        <label>WhatsApp (solo números)</label>
                        <input type="text" name="whatsapp" value="<?= htmlspecialchars($config['whatsapp']) ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div>
                        <label>Dirección</label>
                        <input type="text" name="direccion" value="<?= htmlspecialchars($config['direccion']) ?>">
                    </div>
                    <div>
                        <label>Facebook (URL)</label>
                        <input type="text" name="facebook" value="<?= htmlspecialchars($config['facebook']) ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div>
                        <label>Instagram (URL)</label>
                        <input type="text" name="instagram" value="<?= htmlspecialchars($config['instagram']) ?>">
                    </div>
                </div>

                <h3 style="margin-top: 2rem;">Portada Principal</h3>
                <div class="form-row-full">
                    <label>Foto de la portada principal</label>
                    <?php
                    $ruta_imagen_hero = $config['hero_imagen'] ?? '';
                    $archivo_imagen_hero = __DIR__ . '/../' . ltrim($ruta_imagen_hero, '/');
                    $imagen_hero_actual = $ruta_imagen_hero !== '' && file_exists($archivo_imagen_hero)
                        ? '../' . ltrim($ruta_imagen_hero, '/')
                        : '../assets/img/hero-bg.jpg';
                    ?>
                    <img src="<?= htmlspecialchars($imagen_hero_actual) ?>" alt="Foto actual de la portada" style="width: min(100%, 480px); max-height: 220px; object-fit: cover; margin: 0.5rem 0 1rem;">
                    <input type="file" name="imagen_hero" accept="image/jpeg,image/png,image/webp">
                    <small>Formatos permitidos: JPG, PNG y WEBP. Si no seleccionas una imagen, se conserva la actual.</small>
                </div>
                <div class="form-row">
                    <div>
                        <label>Título Principal</label>
                        <input type="text" name="hero_titulo" value="<?= htmlspecialchars($config['hero_titulo']) ?>">
                    </div>
                    <div>
                        <label>Subtítulo (Texto Naranja)</label>
                        <input type="text" name="hero_subtitulo" value="<?= htmlspecialchars($config['hero_subtitulo']) ?>">
                    </div>
                </div>

                <h3 style="margin-top: 2rem;">Estadísticas</h3>
                <div class="form-row">
                    <div><label>Stat 1 Número</label><input type="text" name="stat_1_numero" value="<?= htmlspecialchars($config['stat_1_numero']) ?>"></div>
                    <div><label>Stat 1 Texto</label><input type="text" name="stat_1_texto" value="<?= htmlspecialchars($config['stat_1_texto']) ?>"></div>
                </div>
                <div class="form-row">
                    <div><label>Stat 2 Número</label><input type="text" name="stat_2_numero" value="<?= htmlspecialchars($config['stat_2_numero']) ?>"></div>
                    <div><label>Stat 2 Texto</label><input type="text" name="stat_2_texto" value="<?= htmlspecialchars($config['stat_2_texto']) ?>"></div>
                </div>
                <div class="form-row">
                    <div><label>Stat 3 Número</label><input type="text" name="stat_3_numero" value="<?= htmlspecialchars($config['stat_3_numero']) ?>"></div>
                    <div><label>Stat 3 Texto</label><input type="text" name="stat_3_texto" value="<?= htmlspecialchars($config['stat_3_texto']) ?>"></div>
                </div>
                <div class="form-row">
                    <div><label>Stat 4 Número</label><input type="text" name="stat_4_numero" value="<?= htmlspecialchars($config['stat_4_numero']) ?>"></div>
                    <div><label>Stat 4 Texto</label><input type="text" name="stat_4_texto" value="<?= htmlspecialchars($config['stat_4_texto']) ?>"></div>
                </div>

                <button type="submit" class="btn-primary" style="margin-top: 1rem;">Guardar Cambios</button>
            </form>
        </div>

        <div class="panel-box">
            <h3>Nuestros Servicios</h3>
            <p>Administra los servicios que aparecen en la página principal. Puedes editar su contenido, cambiar el orden, agregar nuevos o marcar los que quieras eliminar.</p>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="accion" value="guardar_servicios">

                <?php foreach ($serviciosAdmin as $indice => $servicio): ?>
                    <div style="padding: 1rem 0; border-bottom: 1px solid #e5e7eb;">
                        <input type="hidden" name="servicio_id[<?= (int)$indice ?>]" value="<?= (int)$servicio['id'] ?>">
                        <div class="form-row">
                            <div>
                                <label for="servicio_titulo_<?= (int)$servicio['id'] ?>">Título</label>
                                <input id="servicio_titulo_<?= (int)$servicio['id'] ?>" type="text" name="servicio_titulo[<?= (int)$indice ?>]" value="<?= htmlspecialchars($servicio['titulo'], ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                            <div>
                                <label for="servicio_icono_<?= (int)$servicio['id'] ?>">Ícono</label>
                                <?php $iconoActual = $servicio['icono'] ?? 'fa-building'; ?>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <select id="servicio_icono_<?= (int)$servicio['id'] ?>" name="servicio_icono[<?= (int)$indice ?>]" data-icon-preview="icono_vista_<?= (int)$servicio['id'] ?>">
                                        <?php if (!isset($iconosServicio[$iconoActual])): ?>
                                            <option value="<?= htmlspecialchars($iconoActual, ENT_QUOTES, 'UTF-8') ?>" selected>Ícono actual: <?= htmlspecialchars($iconoActual, ENT_QUOTES, 'UTF-8') ?></option>
                                        <?php endif; ?>
                                        <?php foreach ($iconosServicio as $claseIcono => $nombreIcono): ?>
                                            <option value="<?= htmlspecialchars($claseIcono, ENT_QUOTES, 'UTF-8') ?>" <?= $iconoActual === $claseIcono ? 'selected' : '' ?>><?= htmlspecialchars($nombreIcono, ENT_QUOTES, 'UTF-8') ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <i id="icono_vista_<?= (int)$servicio['id'] ?>" class="fas <?= htmlspecialchars($iconoActual, ENT_QUOTES, 'UTF-8') ?>" style="font-size: 1.5rem; color: var(--admin-orange); min-width: 1.5rem; text-align: center;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div>
                                <label for="servicio_descripcion_<?= (int)$servicio['id'] ?>">Descripción</label>
                                <textarea id="servicio_descripcion_<?= (int)$servicio['id'] ?>" name="servicio_descripcion[<?= (int)$indice ?>]" rows="3"><?= htmlspecialchars($servicio['descripcion'], ENT_QUOTES, 'UTF-8') ?></textarea>
                            </div>
                            <div>
                                <label for="servicio_orden_<?= (int)$servicio['id'] ?>">Orden de aparición</label>
                                <input id="servicio_orden_<?= (int)$servicio['id'] ?>" type="number" name="servicio_orden[<?= (int)$indice ?>]" value="<?= (int)$servicio['orden'] ?>">
                                <label style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.75rem;">
                                    <input type="checkbox" name="eliminar_servicio[<?= (int)$servicio['id'] ?>]" value="1" style="width: auto;">
                                    Eliminar este servicio
                                </label>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <h4 style="margin-top: 1.5rem;">Agregar un servicio</h4>
                <div class="form-row">
                    <div>
                        <label for="nuevo_servicio_titulo">Título</label>
                        <input id="nuevo_servicio_titulo" type="text" name="nuevo_servicio_titulo">
                    </div>
                    <div>
                        <label for="nuevo_servicio_icono">Ícono</label>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <select id="nuevo_servicio_icono" name="nuevo_servicio_icono" data-icon-preview="nuevo_icono_vista">
                                <?php foreach ($iconosServicio as $claseIcono => $nombreIcono): ?>
                                    <option value="<?= htmlspecialchars($claseIcono, ENT_QUOTES, 'UTF-8') ?>" <?= $claseIcono === 'fa-building' ? 'selected' : '' ?>><?= htmlspecialchars($nombreIcono, ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                            <i id="nuevo_icono_vista" class="fas fa-building" style="font-size: 1.5rem; color: var(--admin-orange); min-width: 1.5rem; text-align: center;"></i>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div>
                        <label for="nuevo_servicio_descripcion">Descripción</label>
                        <textarea id="nuevo_servicio_descripcion" name="nuevo_servicio_descripcion" rows="3"></textarea>
                    </div>
                    <div>
                        <label for="nuevo_servicio_orden">Orden de aparición</label>
                        <input id="nuevo_servicio_orden" type="number" name="nuevo_servicio_orden" value="<?= count($serviciosAdmin) + 1 ?>">
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="margin-top: 1rem;">Guardar Servicios</button>
            </form>
        </div>
    </main>
    <script>
    document.querySelectorAll('[data-icon-preview]').forEach((selector) => {
        const vistaPrevia = document.getElementById(selector.dataset.iconPreview);
        const actualizarVista = () => {
            vistaPrevia.className = `fas ${selector.value}`;
        };
        selector.addEventListener('change', actualizarVista);
        actualizarVista();
    });
    </script>
</body>
</html>
