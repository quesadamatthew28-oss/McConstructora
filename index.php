<?php
// index.php
require_once 'includes/conexion.php';

function imagen_robusta($ruta, $fallback) {
    $ruta_real = $ruta ?? '';
    if ($ruta_real !== '' && file_exists(__DIR__ . '/' . ltrim($ruta_real, '/'))) {
        return $ruta_real;
    }
    return $fallback;
}

// 1. Obtener configuración de la empresa desde MySQL
$stmt = $pdo->query("SELECT * FROM configuracion_empresa LIMIT 1");
$config = $stmt->fetch();

// 2. Obtener servicios desde MySQL
$stmtServ = $pdo->query("SELECT * FROM servicios ORDER BY orden ASC");
$servicios = $stmtServ->fetchAll();

// 3. Obtener proyectos publicados desde MySQL
$stmtProj = $pdo->query("SELECT * FROM proyectos WHERE estado = 'publicado' ORDER BY fecha_creacion DESC");
$proyectos = $stmtProj->fetchAll();

$imagenesPorProyecto = [];
if (!empty($proyectos)) {
    $idsProyectos = array_column($proyectos, 'id');
    $marcadores = implode(',', array_fill(0, count($idsProyectos), '?'));
    $stmtImagenes = $pdo->prepare("SELECT proyecto_id, ruta_imagen FROM imagenes_proyectos WHERE proyecto_id IN ($marcadores) ORDER BY orden ASC");
    $stmtImagenes->execute($idsProyectos);
    foreach ($stmtImagenes->fetchAll() as $imagen) {
        $imagenesPorProyecto[$imagen['proyecto_id']][] = $imagen['ruta_imagen'];
    }
}

// Incluir la cabecera
require_once 'includes/header.php';
?>

<?php $hero_image = imagen_robusta($config['hero_imagen'] ?? null, 'assets/img/hero-bg.jpg'); ?>
<?php $nosotros_image = imagen_robusta($config['nosotros_imagen'] ?? null, 'assets/img/logo-CostructoraMC.png'); ?>
<!-- ================= HERO ================= -->
<section id="inicio" class="hero" style="background-image: url('<?= htmlspecialchars($hero_image) ?>');">
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <div class="hero-text-wrapper">
            <h1 class="hero-title">
                <?= htmlspecialchars($config['hero_titulo'] ?? 'CONSTRUIMOS EL') ?> <br>
                <span class="text-orange italic"><?= htmlspecialchars($config['hero_subtitulo'] ?? 'FUTURO') ?></span>
            </h1>
            <p class="hero-desc">
                Siempre damos lo mejor. Sin embargo, no solo construimos un edificio, construimos confianza.
            </p>
            <div class="hero-buttons">
                <a href="#proyectos" class="btn btn-primary">EXPLORAR PROYECTOS <i class="fas fa-arrow-down"></i></a>
            </div>
        </div>
    </div>
</section>

<!-- ================= ESTADÍSTICAS ================= -->
<section class="stats-section bg-dark">
    <div class="container stats-grid">
        <div class="stat-item">
            <h3 class="stat-number"><?= htmlspecialchars($config['stat_1_numero'] ?? '+15') ?></h3>
            <p><?= htmlspecialchars($config['stat_1_texto'] ?? 'Años de experiencia') ?></p>
        </div>
        <div class="stat-item">
            <h3 class="stat-number"><?= htmlspecialchars($config['stat_2_numero'] ?? '+250') ?></h3>
            <p><?= htmlspecialchars($config['stat_2_texto'] ?? 'Proyectos completados') ?></p>
        </div>
        <div class="stat-item">
            <h3 class="stat-number"><?= htmlspecialchars($config['stat_3_numero'] ?? '+50') ?></h3>
            <p><?= htmlspecialchars($config['stat_3_texto'] ?? 'Profesionales') ?></p>
        </div>
        <div class="stat-item">
            <h3 class="stat-number"><?= htmlspecialchars($config['stat_4_numero'] ?? '100%') ?></h3>
            <p><?= htmlspecialchars($config['stat_4_texto'] ?? 'Satisfacción') ?></p>
        </div>
    </div>
</section>

<!-- ================= NOSOTROS ================= -->
<section id="nosotros" class="section nosotros-section">
    <div class="container nosotros-container">
        <div class="nosotros-text">
            <h2 class="section-subtitle">NOSOTROS</h2>
            <h3 class="section-title">Construyendo el futuro con bases sólidas</h3>
            <p><?= nl2br(htmlspecialchars($config['texto_nosotros'] ?? '')) ?></p>
            <a href="#contacto" class="btn btn-primary mt-4">Hablemos de tu proyecto</a>
        </div>
        <div class="nosotros-image">
            <img src="<?= htmlspecialchars($nosotros_image) ?>" alt="Nosotros Constructora MC">
        </div>
    </div>
</section>

<!-- ================= SERVICIOS ================= -->
<section id="servicios" class="section bg-light">
    <div class="container">
        <div class="section-header center">
            <h2 class="section-subtitle">NUESTROS SERVICIOS</h2>
            <h3 class="section-title">Soluciones integrales en construcción</h3>
        </div>
        <div class="servicios-grid">
            <?php foreach($servicios as $servicio): ?>
                <div class="servicio-card">
                    <div class="servicio-icon">
                        <i class="fas <?= htmlspecialchars($servicio['icono'] ?? 'fa-building') ?>"></i>
                    </div>
                    <h4><?= htmlspecialchars($servicio['titulo']) ?></h4>
                    <p><?= htmlspecialchars($servicio['descripcion']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ================= PROYECTOS ================= -->
<section id="proyectos" class="section bg-dark">
    <div class="container">
        <div class="section-header">
            <h2 class="section-subtitle text-orange">PORTAFOLIO</h2>
            <h3 class="section-title text-white">Proyectos Destacados</h3>
        </div>
        <div class="proyectos-grid">
            <?php if(empty($proyectos)): ?>
                <p class="text-white">No hay proyectos publicados aún.</p>
            <?php else: ?>
                <?php foreach($proyectos as $proyecto): ?>
                    <div class="proyecto-card">
                        <div class="proyecto-img-wrapper">
                            <?php
                                $portada = imagen_robusta($proyecto['imagen_portada'] ?? null, 'assets/img/placeholder.jpg');
                                $fotos = [$portada];
                                foreach ($imagenesPorProyecto[$proyecto['id']] ?? [] as $rutaFoto) {
                                    $foto = imagen_robusta($rutaFoto, '');
                                    if ($foto !== '' && !in_array($foto, $fotos, true)) {
                                        $fotos[] = $foto;
                                    }
                                }
                            ?>
                            <img
                                src="<?= htmlspecialchars($portada, ENT_QUOTES, 'UTF-8') ?>"
                                data-gallery="<?= htmlspecialchars(json_encode($fotos, JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($proyecto['titulo'], ENT_QUOTES, 'UTF-8') ?>">
                            <?php if (count($fotos) > 1): ?>
                                <div class="proyecto-progreso" aria-hidden="true">
                                    <?php foreach ($fotos as $_foto): ?><span></span><?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="proyecto-info">
                            <span class="proyecto-cat"><?= htmlspecialchars($proyecto['categoria']) ?></span>
                            <h4><?= htmlspecialchars($proyecto['titulo']) ?></h4>
                            <p><i class="fas fa-map-marker-alt text-orange"></i> <?= htmlspecialchars($proyecto['ubicacion']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ================= CONTACTO ================= -->
<section id="contacto" class="section bg-light">
    <div class="container contact-container">
        <div class="contact-info">
            <h2 class="section-subtitle">CONTACTO</h2>
            <h3 class="section-title">¿Listo para construir tu próximo proyecto?</h3>
            <p>Déjanos tus datos y nuestro equipo se pondrá en contacto contigo a la brevedad.</p>
            <div class="info-items">
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h5>Llámanos</h5>
                        <p><?= htmlspecialchars($config['telefono'] ?? '') ?></p>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h5>Escríbenos</h5>
                        <p><?= htmlspecialchars($config['correo_contacto'] ?? '') ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="contact-form-wrapper">
            <form action="procesar_contacto.php" method="POST" class="contact-form">
                <div class="form-group">
                    <input type="text" name="nombre" placeholder="Nombre completo" required>
                </div>
                <div class="form-group">
                    <input type="email" name="correo" placeholder="Correo electrónico" required>
                </div>
                <div class="form-group">
                    <input type="tel" name="telefono" placeholder="Teléfono (Opcional)">
                </div>
                <div class="form-group">
                    <textarea name="mensaje" rows="5" placeholder="Cuéntanos sobre tu proyecto..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Enviar Mensaje</button>
            </form>
        </div>
    </div>
</section>

<script>
document.querySelectorAll('.proyecto-card img[data-gallery]').forEach((imagen) => {
    const fotos = JSON.parse(imagen.dataset.gallery);
    if (fotos.length < 2) return;

    const tarjeta = imagen.closest('.proyecto-card');
    const segmentos = tarjeta.querySelectorAll('.proyecto-progreso span');
    const duracion = 2800;
    let indice = 0;
    let temporizador;

    const mostrarFoto = (nuevoIndice) => {
        indice = nuevoIndice;
        imagen.classList.add('cambiando-foto');
        setTimeout(() => {
            imagen.src = fotos[indice];
            imagen.classList.remove('cambiando-foto');
        }, 160);

        segmentos.forEach((segmento, posicion) => {
            segmento.style.setProperty('--duracion-progreso', `${duracion}ms`);
            segmento.classList.toggle('activo', posicion === indice);
        });
    };

    const avanzar = () => {
        temporizador = setTimeout(() => {
            mostrarFoto((indice + 1) % fotos.length);
            avanzar();
        }, duracion);
    };

    tarjeta.addEventListener('mouseenter', () => {
        mostrarFoto(indice);
        avanzar();
    });

    tarjeta.addEventListener('mouseleave', () => {
        clearTimeout(temporizador);
        indice = 0;
        imagen.src = fotos[0];
        imagen.classList.remove('cambiando-foto');
        segmentos.forEach((segmento) => segmento.classList.remove('activo'));
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
