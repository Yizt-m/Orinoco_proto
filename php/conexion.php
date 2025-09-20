<?php
// --- Configuración de la Base de Datos --- //
$db_host = 'localhost';     // Generalmente 'localhost'
$db_usuario = 'root';       // Usuario de tu base de datos
$db_contrasena = '';        // Contraseña de tu base de datos (vacía por defecto en XAMPP)
$db_nombre = 'orinoco_proto'; // El nombre de tu base de datos

// --- Crear la Conexión --- //
$conexion = new mysqli($db_host, $db_usuario, $db_contrasena, $db_nombre);

// --- Verificar la Conexión --- //
if ($conexion->connect_error) {
    // Si hay un error, se termina la ejecución y se muestra el error.
    die("Error de conexión: " . $conexion->connect_error);
}

// --- Establecer el conjunto de caracteres a UTF-8 --- //
// Esto es importante para evitar problemas con tildes y caracteres especiales.
$conexion->set_charset("utf8");

?>