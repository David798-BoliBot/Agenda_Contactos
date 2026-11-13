<?php

require_once("config/conexion.php");

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $password = $_POST["password"] ?? "";

    // Validar campos
    if ($nombre === "" || $correo === "" || $password === "") {

        $mensaje = "Todos los campos son obligatorios.";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "El correo electrónico no es válido.";

    } elseif (strlen($password) < 8) {

        $mensaje = "La contraseña debe tener al menos 8 caracteres.";

    } else {

        // Comprobar si el correo ya existe
        $consulta = $conexion->prepare(
            "SELECT id FROM usuarios WHERE correo = :correo"
        );

        $consulta->execute([
            ":correo" => $correo
        ]);

        if ($consulta->fetch()) {

            $mensaje = "Este correo ya está registrado.";

        } else {

            // Proteger la contraseña
            $password_seguro = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Guardar el usuario
            $insertar = $conexion->prepare(
                "INSERT INTO usuarios (nombre, correo, password)
                 VALUES (:nombre, :correo, :password)"
            );

            $insertar->execute([
                ":nombre" => $nombre,
                ":correo" => $correo,
                ":password" => $password_seguro
            ]);

            $mensaje = "¡Cuenta creada correctamente!";
        }
    }
}

?>


<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registro | Agenda de Contactos</title>
        <link rel="stylesheet" href="css/estilos.css?v=<?= filemtime(__DIR__ . '/css/estilos.css') ?>">
    </head>

    <body class="pagina-auth">

        <main class="formulario">

            <h1>Crear una cuenta</h1>

            <p>Rellena el siguiente formulario para crear una nueva cuenta.</p>

            <?php if ($mensaje !== ""): ?>
                <p class="mensaje">
                    <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>

            <form action="registro.php" method="POST">

                <label for="nombre">Nombre completo:</label>
                <input type="text" id="nombre" name="nombre" required>

                <label for="correo">Correo electrónico:</label>
                <input type="email" id="correo" name="correo" required>

                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password"
                   minlength="8" required>

                <button type="submit">Registrarse</button>

            </form>

            <p class="texto-enlace">
                ¿Ya tienes una cuenta?
            </p>

            <a href="login.php" class="btn btn-secundario">
                Iniciar sesión
            </a>

        </main>

    </body>
</html>