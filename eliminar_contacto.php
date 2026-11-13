<?php

session_start();

// Comprobar que el usuario haya iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

// Solo aceptar solicitudes POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: contactos.php");
    exit;
}

// Comprobar el token de seguridad
$token = $_POST["token"] ?? "";

if (
    !isset($_SESSION["csrf_token"]) ||
    !is_string($token) ||
    !hash_equals($_SESSION["csrf_token"], $token)
) {
    http_response_code(403);
    exit("Solicitud no autorizada.");
}

// Obtener y validar el ID
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: contactos.php");
    exit;
}

require_once("config/conexion.php");

// Eliminar únicamente si el contacto pertenece al usuario
$consulta = $conexion->prepare(
    "DELETE FROM contactos
     WHERE id = :id AND id_usuario = :usuario_id"
);

$consulta->execute([
    ":id" => $id,
    ":usuario_id" => $_SESSION["usuario_id"]
]);

// Regresar a la lista de contactos
header("Location: contactos.php");
exit;

?>