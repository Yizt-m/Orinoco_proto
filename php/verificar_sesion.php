<?php
// Reanudamos la sesión que se inició en login_proceso.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si no existen las variables de sesión de usuario, significa que no ha iniciado sesión.
if (!isset($_SESSION['usuario_id'])) {
    // Lo redirigimos al login.
    header('Location: index.php');
    exit(); // Detenemos la ejecución del script.
}
?>