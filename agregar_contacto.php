<?php

session_start();

// Comprobar que el usuario haya iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

// Crear token de seguridad si todavía no existe
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

require_once("config/conexion.php");

$mensaje = "";

// Obtener las categorías disponibles
$consulta_categorias = $conexion->query(
    "SELECT id, nombre FROM categorias ORDER BY nombre ASC"
);

$categorias = $consulta_categorias->fetchAll();

// Obtener el ID de la categoría Otro para usarla por defecto
$consulta_otro = $conexion->prepare(
    "SELECT id FROM categorias WHERE nombre = 'Otro'"
);

$consulta_otro->execute();
$categoria_otro = $consulta_otro->fetchColumn();

// Valores iniciales del formulario
$nombre = "";
$telefono = "";
$correo = "";
$ciudad = "";
$pais = "";
$id_categoria = $categoria_otro !== false ? (int) $categoria_otro : 0;
$favorito = "0";

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

    $id_categoria = filter_var(
        $_POST["id_categoria"] ?? "",
        FILTER_VALIDATE_INT
    );

    $favorito = $_POST["favorito"] ?? "0";

    // Validar campos obligatorios
    if ($nombre === "" || $telefono === "") {

        $mensaje = "El nombre y el teléfono son obligatorios.";

    } elseif ($correo !== "" && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "El correo electrónico no es válido.";

    } elseif (
        $id_categoria === false ||
        !in_array($id_categoria, array_column($categorias, "id"))
    ) {

        $mensaje = "La categoría seleccionada no es válida.";

    } elseif (!in_array($favorito, ["0", "1"], true)) {

        $mensaje = "La opción de favorito no es válida.";

    } else {

        // Obtener el ID del usuario conectado
        $usuario_id = $_SESSION["usuario_id"];

        // Insertar el contacto asociado al usuario
        $consulta = $conexion->prepare(
            "INSERT INTO contactos
            (id_usuario, id_categoria, favorito, nombre, telefono, correo, ciudad, pais)
            VALUES
            (:id_usuario, :id_categoria, :favorito, :nombre, :telefono, :correo, :ciudad, :pais)"
        );

        $consulta->execute([
            ":id_usuario" => $usuario_id,
            ":id_categoria" => $id_categoria,
            ":favorito" => (int) $favorito,
            ":nombre" => $nombre,
            ":telefono" => $telefono,
            ":correo" => $correo !== "" ? $correo : null,
            ":ciudad" => $ciudad !== "" ? $ciudad : null,
            ":pais" => $pais !== "" ? $pais : null
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

                <input
                    type="hidden"
                    name="token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                <label for="nombre">Nombre completo:</label>

                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    value="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>"
                    required>

                <label for="telefono">Teléfono:</label>

                <input
                    type="text"
                    id="telefono"
                    name="telefono"
                    value="<?= htmlspecialchars($telefono, ENT_QUOTES, 'UTF-8') ?>"
                    required>

                <label for="correo">Correo electrónico:</label>

                <input
                    type="email"
                    id="correo"
                    name="correo"
                    value="<?= htmlspecialchars($correo, ENT_QUOTES, 'UTF-8') ?>">

                <label for="ciudad">Ciudad:</label>

                <input
                    type="text"
                    id="ciudad"
                    name="ciudad"
                    value="<?= htmlspecialchars($ciudad, ENT_QUOTES, 'UTF-8') ?>">

                <label for="pais">País:</label>

                <input
                    type="text"
                    id="pais"
                    name="pais"
                    value="<?= htmlspecialchars($pais, ENT_QUOTES, 'UTF-8') ?>">

                <label for="id_categoria">Categoría:</label>

                <select id="id_categoria" name="id_categoria" required>

                    <?php foreach ($categorias as $categoria): ?>

                        <option
                            value="<?= (int) $categoria["id"] ?>"
                            <?= $id_categoria == $categoria["id"] ? "selected" : "" ?>>

                            <?= htmlspecialchars($categoria["nombre"], ENT_QUOTES, 'UTF-8') ?>

                        </option>

                    <?php endforeach; ?>

                </select>

                <label for="favorito">¿Es un contacto favorito?</label>

                <select id="favorito" name="favorito" required>

                    <option value="0" <?= $favorito === "0" ? "selected" : "" ?>>
                        No
                    </option>

                    <option value="1" <?= $favorito === "1" ? "selected" : "" ?>>
                        Sí
                    </option>

                </select>

                <button type="submit">Guardar contacto</button>

            </form>

            <a href="contactos.php" class="btn btn-secundario btn-volver">
                Volver a mis contactos
            </a>

        </main>

    </body>

</html>