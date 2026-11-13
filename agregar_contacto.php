<?php

session_start();

// Comprobar que el usuario haya iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

require_once("config/conexion.php");

$mensaje = "";

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $token = $_POST["token"] ?? "";

    if (!hash_equals($_SESSION["csrf_token"], $token)) {
        http_response_code(403);
        exit("Error de Seguridad: Solicitud no autorizada.");
    }


    $nombre = trim($_POST["nombre"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $ciudad = trim($_POST["ciudad"] ?? "");
    $pais = trim($_POST["pais"] ?? "");

    // Validar los campos obligatorios
    if ($nombre === "" || $telefono === "") {

        $mensaje = "El nombre y el teléfono son obligatorios.";

    } elseif ($correo !== "" && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "El correo electrónico no es válido.";

    } else {

        // Obtener el ID del usuario conectado
        $usuario_id = $_SESSION["usuario_id"];

        // Insertar el contacto asociado a ese usuario
        $consulta = $conexion->prepare(
            "INSERT INTO contactos
            (id_usuario, nombre, telefono, correo, ciudad, pais)
            VALUES
            (:id_usuario, :nombre, :telefono, :correo, :ciudad, :pais)"
        );

        $consulta->execute([
            ":id_usuario" => $usuario_id,
            ":nombre" => $nombre,
            ":telefono" => $telefono,
            ":correo" => $correo !== "" ? $correo : null,
            ":pais" => $pais !== "" ? $pais : null,
            ":ciudad" => $ciudad !== "" ? $ciudad : null
        ]);

        // Regresar a la lista de contactos
        header("Location: contactos.php");
        exit;
    }
}

?>


<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Agregar contacto | Agenda de Contactos</title>
        <link rel="stylesheet" href="css/estilos.css?v=<?= filemtime(__DIR__ . '/css/estilos.css') ?>">
    </head>

    <body class="pagina-formulario">

        <main class="formulario formulario-contacto">

            <h1>Agregar nuevo contacto</h1>

            <p>Completa los datos para guardar un nuevo contacto en tu agenda.</p>

            <?php if ($mensaje !== ""): ?>
                <p class="mensaje">
                    <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>

            <form action="agregar_contacto.php" method="POST">

                <input type="hidden" name="token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                <label for="nombre">Nombre completo:</label>
                <input type="text" id="nombre" name="nombre" required>

                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" required>

                <label for="correo">Correo electrónico:</label>
                <input type="email" id="correo" name="correo">

                <label for="ciudad">Ciudad:</label>
                <input type="text" id="ciudad" name="ciudad">

                <label for="pais">País:</label>
                <input type="text" id="pais" name="pais">

                <button type="submit">Guardar contacto</button>

            </form>

            <a href="contactos.php" class="btn btn-secundario btn-volver">
                Volver a mis contactos
            </a>

        </main>

    </body>
</html>