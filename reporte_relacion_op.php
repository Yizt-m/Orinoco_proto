<?php
// Primero, verificamos que el usuario haya iniciado sesión.
require_once 'php/verificar_sesion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Relación de Operaciones - Orinoco Proto</title>
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
            <h1>Generador de Reporte Relación de Operaciones</h1>
            <p>Por favor, cargue los archivos correspondientes para generar el reporte.</p>
            
            <form action="php/procesar_relacion_op.php" method="POST" enctype="multipart/form-data" style="margin-top: 2rem;">
                
                <div class="form-group">
                    <label for="archivo_p01">
                        <strong>Archivo P01 (Formato: .txt)</strong>
                    </label>
                    <input type="file" id="archivo_p01" name="archivo_p01" class="input-field" required accept=".txt">
                </div>

                <div class="form-group">
                    <label for="archivo_d01">
                        <strong>Archivo D01 (Formato: .csv)</strong>
                    </label>
                    <input type="file" id="archivo_d01" name="archivo_d01" class="input-field" required accept=".csv">
                </div>
                
                <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-cogs"></i> Generar Reporte
                </button>
                
            </form>
        </div>
    </main>

    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');

        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    </script>

</body>
</html>