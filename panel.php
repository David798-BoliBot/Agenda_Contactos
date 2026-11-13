<?php

session_start();

// Comprobar si el usuario inició sesión
if (!isset($_SESSION["usuario_id"])) {

    header("Location: login.php");
    exit;
}

$nombre = $_SESSION["usuario_nombre"];

?>


<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Panel principal | Agenda de Contactos</title>
        <link rel="stylesheet" href="css/estilos.css?v=<?= filemtime(__DIR__ . '/css/estilos.css') ?>">
    </head>

    <body class="pagina-panel">

        <main class="panel">

            <h1>Panel principal</h1>

            <h2>
                ¡Hola de nuevo, <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>!
            </h2>

            <p>Has iniciado sesión correctamente.</p>

            <p>Este es tu espacio personal de contactos.</p>

            <nav class="panel-navegacion">

                <a href="contactos.php" class="btn btn-principal">
                    Ver mis contactos
                </a>

                <a href="logout.php" class="btn btn-salir">
                    Cerrar sesión
                </a>

            </nav>

        </main>

    </body>
</html>
