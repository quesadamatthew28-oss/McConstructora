<?php
// includes/footer.php
?>
    <footer class="footer">
        <div class="container footer-container">
            <div class="footer-col">
                <img src="assets/img/logo-CostructoraMC.png" alt="Constructora MC" class="footer-logo">
                <p><?= htmlspecialchars($config['descripcion_corta'] ?? '') ?></p>
            </div>
            <div class="footer-col">
                <h4>Contacto</h4>
                <ul class="footer-contact">
                    <li><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($config['direccion'] ?? 'Dirección no configurada') ?></li>
                    <li><i class="fas fa-phone"></i> <?= htmlspecialchars($config['telefono'] ?? '') ?></li>
                    <li><i class="fas fa-envelope"></i> <?= htmlspecialchars($config['correo_contacto'] ?? '') ?></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Síguenos</h4>
                <div class="social-links">
                    <?php if(!empty($config['facebook'])): ?>
                        <a href="<?= htmlspecialchars($config['facebook']) ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if(!empty($config['instagram'])): ?>
                        <a href="<?= htmlspecialchars($config['instagram']) ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                    <?php if(!empty($config['whatsapp'])): ?>
                        <a href="https://wa.me/<?= htmlspecialchars($config['whatsapp']) ?>" target="_blank"><i class="fab fa-whatsapp"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($config['nombre_empresa'] ?? 'Constructora MC') ?>. Todos los derechos reservados.</p>
        </div>
    </footer>

    <?php if(!empty($config['whatsapp'])): ?>
    <a href="https://wa.me/<?= htmlspecialchars($config['whatsapp']) ?>" class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>
    <?php endif; ?>

    <script src="assets/js/script.js?v=2"></script>
</body>
</html>
