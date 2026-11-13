<?php

session_start();

require_once("config/conexion.php");

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $correo = trim($_POST["correo"] ?? "");
    $password = $_POST["password"] ?? "";

    // Validar los campos
    if ($correo === "" || $password === "") {

        $mensaje = "Debes completar todos los campos.";

    } else {

        // Buscar al usuario por su correo
        $consulta = $conexion->prepare(
            "SELECT id, nombre, correo, password
             FROM usuarios
             WHERE correo = :correo"
        );

        $consulta->execute([
            ":correo" => $correo
        ]);

        $usuario = $consulta->fetch();

        // Comprobar la contraseña
        if ($usuario && password_verify($password, $usuario["password"])) {

            // Regenerar el identificador de sesión
            session_regenerate_id(true);

            // Guardar los datos necesarios en la sesión
            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["usuario_nombre"] = $usuario["nombre"];
            $_SESSION["usuario_correo"] = $usuario["correo"];

            // Ir al panel principal
            header("Location: panel.php");
            exit;

        } else {

            $mensaje = "Correo o contraseña incorrectos.";
        }
    }
}

?>


<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Iniciar sesión | Agenda de Contactos</title>
        <link rel="stylesheet" href="css/estilos.css?v=<?= filemtime(__DIR__ . '/css/estilos.css') ?>">
    </head>

    <body class="pagina-auth">

        <main class="formulario">

            <h1>Iniciar sesión</h1>

            <p>Introduce tus credenciales para acceder a tu cuenta.</p>

            <?php if ($mensaje !== ""): ?>
                <p class="mensaje">
                    <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>

            <form action="login.php" method="POST">

                <label for="correo">Correo electrónico:</label>
                <input type="email" id="correo" name="correo" required>

                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Iniciar sesión</button>

            </form>

            <p class="texto-enlace">
                ¿No tienes una cuenta?
            </p>

            <a href="registro.php" class="btn btn-secundario">
            Crear cuenta
            </a>

        </main>

    </body>
</html>
