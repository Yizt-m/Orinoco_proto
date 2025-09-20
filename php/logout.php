<?php
// Es crucial iniciar la sesión para poder acceder a ella y destruirla.
session_start();

// 1. Elimina todas las variables de sesión.
$_SESSION = array();

// 2. Destruye la sesión por completo.
session_destroy();

// 3. Redirige al index.php con un parámetro de éxito.
//    Este '?logout=success' es la clave para mostrar el mensaje.
header('Location: ../index.php?logout=success');
exit();
?>