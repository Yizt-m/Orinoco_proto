<?php
// Incluimos el script de seguridad para verificar si la sesión está activa.
// Es lo primero que se debe hacer en una página protegida.
require_once 'php/verificar_sesion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal - Orinoco Proto</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="main-app">

    <button class="menu-toggle" id="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>

    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>Orinoco</h3>
        </div>
        <a href="reporte_at48.php">AT48</a>
        <a href="reporte_at43.php">AT43</a>
        <a href="reporte_conciliaciones.php">Conciliaciones</a>
        <a href="reporte_relacion_op.php">Relación de Operaciones</a>
        <a href="reporte_reembolsos.php">Reporte de Reembolsos</a>
        
        <a href="php/logout.php" class="logout-link">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </a>
    </nav>

    <main class="main-content" id="main-content">
        <div class="content-wrapper">
            <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>!</h1>
            <p>Selecciona una opción del menú lateral para generar un nuevo reporte.</p>
            <p>Haz clic en el botón <i class="fas fa-bars"></i> para mostrar u ocultar el menú.</p>
        </div>
        <br><br>
        <div class="content-wrapper">
            <h2>Información Importante</h2>
            <ul>
                <li>Los reportes deben ser cargados en el formato correspondiente.</li>
                <li>Asegúrate de tener los archivos listo para ser procesado.</li>
                <li>Si tienes problemas para generar un reporte, contacta al administrador del sistema.</li>
            </ul>
    </main>

    <script>
        // Obtenemos los elementos del DOM
        const menuToggle = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');

        // Añadimos un evento 'click' al botón
        menuToggle.addEventListener('click', () => {
            // La función classList.toggle() añade o quita la clase 'active'
            sidebar.classList.toggle('active');
        });
    </script>

</body>
</html>