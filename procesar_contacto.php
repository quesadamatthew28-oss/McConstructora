<?php
require_once __DIR__ . '/includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$correo = filter_var(trim($_POST['correo'] ?? ''), FILTER_VALIDATE_EMAIL);
$telefono = trim($_POST['telefono'] ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');

if ($nombre === '' || !$correo || $mensaje === '') {
    http_response_code(400);
    echo <<<'HTML'
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos incompletos</title>
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            padding: 1.5rem;
            display: grid;
            place-items: center;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            color: #292929;
            background-color: #17191d;
            background-image:
                radial-gradient(ellipse at 15% 20%, rgba(166, 20, 27, 0.3) 0%, transparent 38%),
                radial-gradient(ellipse at 85% 80%, rgba(92, 20, 30, 0.28) 0%, transparent 42%),
                linear-gradient(135deg, #202329 0%, #111215 100%);
        }

        .card {
            position: relative;
            width: min(100%, 500px);
            padding: 2.5rem;
            box-sizing: border-box;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.65);
            border-radius: 20px;
            background: #fff;
            text-align: center;
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.35);
        }

        .card::before {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #821018, #d64a1d);
            content: '';
        }

        .card-icon {
            display: grid;
            width: 64px;
            height: 64px;
            margin: 0 auto 1.25rem;
            place-items: center;
            border-radius: 50%;
            background: rgba(166, 20, 27, 0.1);
            color: #a6141b;
            font-size: 2rem;
            font-weight: 700;
        }

        h1 {
            margin: 0 0 0.75rem;
            color: #a6141b;
            font-size: 1.8rem;
        }

        .card p {
            margin: 0;
            color: #62666d;
            font-size: 0.98rem;
            line-height: 1.7;
        }

        a {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.85rem 1.3rem;
            border-radius: 9px;
            background: #a6141b;
            color: #fff;
            font-weight: 700;
            text-decoration: none;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        a:hover {
            transform: translateY(-2px);
            background: #821018;
            color: #fff;
        }
    </style>
</head>
<body>
    <main class="card">
        <div class="card-icon" aria-hidden="true">!</div>
        <h1>Datos incompletos</h1>
        <p>Por favor, completa todos los campos obligatorios antes de enviar el mensaje.</p>
        <a href="index.php#contacto">Volver al formulario</a>
    </main>
</body>
</html>
HTML;
    exit;
}

$stmt = $pdo->prepare("INSERT INTO mensajes_contacto (nombre, correo, telefono, mensaje) VALUES (?, ?, ?, ?)");
$stmt->execute([$nombre, $correo, $telefono, $mensaje]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensaje enviado</title>
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            padding: 1.5rem;
            display: grid;
            place-items: center;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            color: #292929;
            background-color: #17191d;
            background-image:
                radial-gradient(ellipse at 15% 20%, rgba(166, 20, 27, 0.3) 0%, transparent 38%),
                radial-gradient(ellipse at 85% 80%, rgba(92, 20, 30, 0.28) 0%, transparent 42%),
                linear-gradient(135deg, #202329 0%, #111215 100%);
        }

        .card {
            position: relative;
            width: min(100%, 500px);
            padding: 2.5rem;
            box-sizing: border-box;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.65);
            border-radius: 20px;
            background: #fff;
            text-align: center;
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.35);
        }

        .card::before {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #821018, #d64a1d);
            content: '';
        }

        .card-icon {
            display: grid;
            width: 64px;
            height: 64px;
            margin: 0 auto 1.25rem;
            place-items: center;
            border-radius: 50%;
            background: rgba(39, 174, 96, 0.12);
            color: #218c50;
            font-size: 1.8rem;
            font-weight: 700;
        }

        h1 {
            margin: 0 0 0.75rem;
            color: #202329;
            font-size: 1.8rem;
        }

        .card p {
            margin: 0;
            color: #62666d;
            font-size: 0.98rem;
            line-height: 1.7;
        }

        a {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.85rem 1.3rem;
            border-radius: 9px;
            background: #a6141b;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        a:hover {
            transform: translateY(-2px);
            background: #821018;
        }
    </style>
</head>
<body>
    <main class="card">
        <div class="card-icon" aria-hidden="true">✓</div>
        <h1>¡Mensaje enviado!</h1>
        <p>Gracias por contactarnos. Nuestro equipo te responderá a la brevedad.</p>
        <a href="index.php">Volver a la página principal</a>
    </main>
</body>
</html>
