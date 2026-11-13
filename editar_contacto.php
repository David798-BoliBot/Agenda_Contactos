<?php

session_start();

// Comprobar sesión
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

require_once("config/conexion.php");

$usuario_id = $_SESSION["usuario_id"];
$mensaje = "";

// Obtener el ID del contacto
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["token"] ?? "";

    if (!hash_equals($_SESSION["csrf_token"], $token)) {
        http_response_code(403);
        exit("Error de Seguridad: Solicitud no autorizada.");
    }

    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
}

// Comprobar que el ID sea válido
if (!$id || $id < 1) {
    header("Location: contactos.php");
    exit;
}

// Buscar el contacto y comprobar que pertenezca al usuario
$consulta = $conexion->prepare(
    "SELECT * FROM contactos
     WHERE id = :id AND id_usuario = :usuario_id"
);

$consulta->execute([
    ":id" => $id,
    ":usuario_id" => $usuario_id
]);

$contacto = $consulta->fetch();

// Si no existe o no pertenece al usuario, no permitir editarlo
if (!$contacto) {
    http_response_code(404);
    exit("Contacto no encontrado.");
}

// Procesar las modificaciones
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $ciudad = trim($_POST["ciudad"] ?? "");
    $pais = trim($_POST["pais"] ?? "");

    if ($nombre === "" || $telefono === "") {

        $mensaje = "El nombre y el teléfono son obligatorios.";

    } elseif ($correo !== "" && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "El correo electrónico no es válido.";

    } else {

        $actualizar = $conexion->prepare(
            "UPDATE contactos
             SET nombre = :nombre,
                 telefono = :telefono,
                 correo = :correo,
                 ciudad = :ciudad,
                 pais = :pais
             WHERE id = :id AND id_usuario = :usuario_id"
        );

        $actualizar->execute([
            ":nombre" => $nombre,
            ":telefono" => $telefono,
            ":correo" => $correo !== "" ? $correo : null,
            ":ciudad" => $ciudad !== "" ? $ciudad : null,
            ":pais" => $pais !== "" ? $pais : null,
            ":id" => $id,
            ":usuario_id" => $usuario_id
        ]);

        header("Location: contactos.php");
        exit;
    }

    // Mantener los datos introducidos si hay un error
    $contacto["nombre"] = $nombre;
    $contacto["telefono"] = $telefono;
    $contacto["correo"] = $correo;
    $contacto["ciudad"] = $ciudad;
    $contacto["pais"] = $pais;
}

?>


<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Editar contacto | Agenda de Contactos</title>
        <link rel="stylesheet" href="css/estilos.css?v=<?= filemtime(__DIR__ . '/css/estilos.css') ?>">
    </head>

    <body class="pagina-formulario">

        <main class="formulario formulario-contacto">

            <h1>Editar contacto</h1>

            <p>Modifica la información del contacto seleccionado.</p>

            <?php if ($mensaje !== ""): ?>
                <p class="mensaje">
                    <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>

            <form action="editar_contacto.php?id=<?= (int) $id ?>" method="POST">

                <input type="hidden" name="token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                <input type="hidden" name="id" value="<?= (int) $id ?>">

                <label for="nombre">Nombre completo:</label>
                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    value="<?= htmlspecialchars($contacto["nombre"], ENT_QUOTES, 'UTF-8') ?>"
                    required>

                <label for="telefono">Teléfono:</label>
                <input
                    type="text"
                    id="telefono"
                    name="telefono"
                    value="<?= htmlspecialchars($contacto["telefono"], ENT_QUOTES, 'UTF-8') ?>"
                    required>

                <label for="correo">Correo electrónico:</label>
                <input
                    type="email"
                    id="correo"
                    name="correo"
                    value="<?= htmlspecialchars($contacto["correo"] ?? "", ENT_QUOTES, 'UTF-8') ?>">

                <label for="ciudad">Ciudad:</label>
                <input
                    type="text"
                    id="ciudad"
                    name="ciudad"
                    value="<?= htmlspecialchars($contacto["ciudad"] ?? "", ENT_QUOTES, 'UTF-8') ?>">

                <label for="pais">País:</label>
                <input
                    type="text"
                    id="pais"
                    name="pais"
                    value="<?= htmlspecialchars($contacto["pais"] ?? "", ENT_QUOTES, 'UTF-8') ?>">

                <button type="submit">Guardar cambios</button>

            </form>

            <a href="contactos.php" class="btn btn-secundario">
                Cancelar y volver
            </a>

        </main>

    </body>
</html>