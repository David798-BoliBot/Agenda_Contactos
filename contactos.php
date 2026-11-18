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

// Obtener ID del usuario conectado
$usuario_id = $_SESSION["usuario_id"];

// Obtener búsqueda
$busqueda = trim($_GET["buscar"] ?? "");

// Obtener filtro de favoritos
$filtro_favoritos = $_GET["favoritos"] ?? "todos";

if (!in_array($filtro_favoritos, ["todos", "si"], true)) {
    $filtro_favoritos = "todos";
}

// Obtener ordenamiento
$orden = $_GET["orden"] ?? "nombre";

// Lista permitida de columnas para ordenar
$ordenamientos = [
    "nombre" => "c.nombre ASC",
    "ciudad" => "c.ciudad ASC",
    "fecha" => "c.fecha_creacion DESC"
];

if (!array_key_exists($orden, $ordenamientos)) {
    $orden = "nombre";
}

$orden_sql = $ordenamientos[$orden];

// Construir consulta
$sql = "
    SELECT
        c.id,
        c.nombre,
        c.telefono,
        c.correo,
        c.ciudad,
        c.pais,
        c.favorito,
        c.fecha_creacion,
        cat.nombre AS categoria
    FROM contactos c
    INNER JOIN categorias cat
        ON c.id_categoria = cat.id
    WHERE c.id_usuario = :usuario_id
";

$parametros = [
    ":usuario_id" => $usuario_id
];

// Aplicar búsqueda
if ($busqueda !== "") {
    $sql .= "
        AND (
            c.nombre LIKE :nombre
            OR c.correo LIKE :correo
            OR c.ciudad LIKE :ciudad
        )
    ";

    $termino = "%" . $busqueda . "%";

    $parametros[":nombre"] = $termino;
    $parametros[":correo"] = $termino;
    $parametros[":ciudad"] = $termino;
}

// Mostrar solamente favoritos si se seleccionó el filtro
if ($filtro_favoritos === "si") {
    $sql .= " AND c.favorito = 1";
}

// Aplicar ordenamiento
$sql .= " ORDER BY " . $orden_sql;

// Ejecutar consulta
$consulta = $conexion->prepare($sql);
$consulta->execute($parametros);

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

                <!-- Busqueda y filtros -->
                <form method="GET" action="contactos.php" class="filtros-contactos">

                    <div>
                        <label for="buscar">Buscar contacto:</label>

                        <input
                            type="text"
                            id="buscar"
                            name="buscar"
                            placeholder="Nombre, correo o ciudad..."
                            value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div>
                        <label for="orden">Ordenar por:</label>

                        <select name="orden" id="orden">

                            <option value="nombre" <?= $orden === "nombre" ? "selected" : "" ?>>
                                Nombre
                            </option>

                            <option value="ciudad" <?= $orden === "ciudad" ? "selected" : "" ?>>
                                Ciudad
                            </option>

                            <option value="fecha" <?= $orden === "fecha" ? "selected" : "" ?>>
                                Fecha de creación
                            </option>

                        </select>
                    </div>

                    <div>
                        <label for="favoritos">Mostrar:</label>

                        <select name="favoritos" id="favoritos">

                            <option value="todos" <?= $filtro_favoritos === "todos" ? "selected" : "" ?>>
                                Todos los contactos
                            </option>

                            <option value="si" <?= $filtro_favoritos === "si" ? "selected" : "" ?>>
                                Solo favoritos
                            </option>

                        </select>
                    </div>

                    <button type="submit" class="btn btn-principal">
                        Aplicar filtros
                    </button>

                    <a href="contactos.php" class="btn btn-secundario">
                        Limpiar
                    </a>

                </form>

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
                                    <th>Categoría</th>
                                    <th>Favorito</th>
                                    <th>Fecha de creación</th>
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
                                            <?= htmlspecialchars($contacto["categoria"], ENT_QUOTES, 'UTF-8') ?>
                                        </td>

                                        <td>
                                            <?= $contacto["favorito"] ? "⭐ Sí" : "No" ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($contacto["fecha_creacion"], ENT_QUOTES, 'UTF-8') ?>
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

                        <h3>No se encontraron contactos</h3>

                        <?php if ($busqueda !== "" || $filtro_favoritos === "si"): ?>

                            <p>No hay contactos que coincidan con los filtros seleccionados.</p>

                            <a href="contactos.php" class="btn btn-secundario">
                            Ver todos los contactos
                            </a>

                        <?php else: ?>

                            <p>Todavía no tienes contactos registrados.</p>
                            <p>¡Agrega tu primer contacto para comenzar!</p>

                        <?php endif; ?>

                    </div>

                <?php endif; ?>

            </section>

        </main>

    </body>

</html>
