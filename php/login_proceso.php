<?php
// Iniciar la sesión es lo PRIMERO que se debe hacer.
session_start();

// Incluir el archivo de conexión a la base de datos.
require_once 'conexion.php';

// Verificar si los datos fueron enviados por el método POST.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Obtener el id y la contraseña del formulario.
    $id = $_POST['id'];
    $contrasena_ingresada = $_POST['contrasena'];

    // --- Consulta Preparada para Evitar Inyección SQL --- //
    // 1. Preparar la consulta
    $stmt = $conexion->prepare("SELECT id, nombre_usuario, contrasena FROM usuario WHERE id = ?");

    // 2. Vincular el parámetro (la 'i' significa que es un entero)
    $stmt->bind_param("i", $id);

    // 3. Ejecutar la consulta
    $stmt->execute();

    // 4. Obtener el resultado
    $resultado = $stmt->get_result();

    // 5. Verificar si se encontró un usuario
    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        
        // --- Verificar la Contraseña --- //
        // Comparamos la contraseña ingresada con el hash guardado en la BD.
        if (password_verify($contrasena_ingresada, $usuario['contrasena'])) {
            // ¡Contraseña correcta!
            
            // Guardar datos del usuario en la sesión.
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre_usuario'];
            
            // Redirigir al menú principal.
            header('Location: ../principal.php');
            exit();

        }
    }

    // Si el usuario no existe o la contraseña es incorrecta, redirigir de vuelta al login
    // con un parámetro de error para mostrar la alerta.
    header('Location: ../index.php?error=1');
    exit();

} else {
    // Si alguien intenta acceder a este archivo directamente, lo redirigimos al login.
    header('Location: ../index.php');
    exit();
}
?>