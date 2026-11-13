<?php

session_start();

// Comprobar que el usuario haya iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

// Crear un token de seguridad si todavía no existe
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

require_once("config/conexion.php");

// Obtener el ID del usuario conectado
$usuario_id = $_SESSION["usuario_id"];

// Consultar únicamente los contactos de ese usuario
$consulta = $conexion->prepare(
    "SELECT id, nombre, telefono, correo, ciudad, pais
     FROM contactos
     WHERE id_usuario = :usuario_id
     ORDER BY nombre ASC"
);

$consulta->execute([
    ":usuario_id" => $usuario_id
]);

$contactos = $consulta->fetchAll();

?>


<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mis contactos | Agenda de Contactos</title>
        <link rel="stylesheet" href="css/estilos.css?v=<?= filemtime(__DIR__ . '/css/estilos.css') ?>">
    </head>

    <body class="pagina-contactos">

        <main class="contenedor agenda-contenedor">

            <header class="agenda-cabecera">

                <h1>Mi agenda de contactos</h1>

                <p>
                    Bienvenido,
                    <?= htmlspecialchars($_SESSION["usuario_nombre"], ENT_QUOTES, 'UTF-8') ?>
                </p>

            </header>

            <nav class="agenda-navegacion">

                <a href="panel.php" class="btn btn-secundario">
                    Volver al panel
                </a>

                <a href="agregar_contacto.php" class="btn btn-principal">
                    Agregar nuevo contacto
                </a>

            </nav>

            <section class="lista-contactos">

                <h2>Mis contactos</h2>

                <?php if (count($contactos) > 0): ?>

                    <div class="tabla-responsive">

                        <table class="tabla-contactos">

                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Teléfono</th>
                                    <th>Correo</th>
                                    <th>Ciudad</th>
                                    <th>País</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($contactos as $contacto): ?>

                                    <tr>

                                        <td>
                                            <?= htmlspecialchars($contacto["nombre"], ENT_QUOTES, 'UTF-8') ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($contacto["telefono"], ENT_QUOTES, 'UTF-8') ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($contacto["correo"] ?? "", ENT_QUOTES, 'UTF-8') ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($contacto["ciudad"] ?? "", ENT_QUOTES, 'UTF-8') ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($contacto["pais"] ?? "", ENT_QUOTES, 'UTF-8') ?>
                                        </td>

                                        <td>

                                            <div class="acciones">

                                                <a
                                                    class="btn btn-editar"
                                                    href="editar_contacto.php?id=<?= (int) $contacto["id"] ?>">
                                                    Editar
                                                </a>

                                                <form
                                                    action="eliminar_contacto.php"
                                                    method="POST"
                                                    onsubmit="return confirm('¿Estás seguro de eliminar este contacto?');">

                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?= (int) $contacto["id"] ?>">

                                                    <input
                                                        type="hidden"
                                                        name="token"
                                                        value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                                                    <button type="submit" class="btn btn-eliminar">
                                                        Eliminar
                                                    </button>

                                                </form>

                                            </div>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php else: ?>

                    <div class="mensaje-vacio">
                        <h3>Tu agenda está vacía</h3>
                        <p>Todavía no tienes contactos registrados.</p>
                        <p>¡Agrega tu primer contacto para comenzar!</p>
                    </div>

                <?php endif; ?>

            </section>

        </main>

    </body>
</html>