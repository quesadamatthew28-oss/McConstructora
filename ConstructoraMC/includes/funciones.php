<?php
// includes/funciones.php
// Funciones auxiliares

// Limpiar datos de entrada
function limpiar($dato) {
    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}

// Redireccionar
function redireccionar($url) {
    header("Location: $url");
    exit;
}

// Validar y subir imagen
function subir_imagen($archivo, $directorio = '../uploads/') {
    if (!isset($archivo['tmp_name']) || empty($archivo['tmp_name'])) {
        return false;
    }

    // Validar tamaño (máx 700 MB)
    if ($archivo['size'] > 700 * 1024 * 1024) {
        return false;
    }

    // Validar tipo real
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $archivo['tmp_name']);
    finfo_close($finfo);

    $tipos_permitidos = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($mime, $tipos_permitidos)) {
        return false;
    }

    // Generar nombre aleatorio
    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    $nuevo_nombre = bin2hex(random_bytes(16)) . '.' . strtolower($extension);
    $ruta_destino = $directorio . $nuevo_nombre;

    if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
        return $nuevo_nombre;
    }
    return false;
}
?>
