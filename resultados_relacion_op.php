<?php
require_once 'php/verificar_sesion.php';

if (!isset($_SESSION['resultado_reporte'])) {
    header('Location: principal.php');
    exit();
}

$resultado_json = $_SESSION['resultado_reporte'];
unset($_SESSION['resultado_reporte']); // Limpiamos la sesión

$datos = json_decode($resultado_json, true);
$status = $datos['status'] ?? 'error';
$mensaje = $datos['message'] ?? 'Ocurrió un error desconocido.';
$url_descarga = $datos['download_url'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados del Reporte - Relación de Operaciones</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="main-app">
    <button class="menu-toggle" id="menu-toggle"><i class="fas fa-bars"></i></button>
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header"><h3>Orinoco</h3></div>
        <a href="reporte_at48.php">AT48</a><a href="reporte_at43.php">AT43</a>
        <a href="reporte_conciliaciones.php">Conciliaciones</a><a href="reporte_relacion_op.php">Relación de Operaciones</a>
        <a href="reporte_reembolsos.php">Reporte de Reembolsos</a>
        <a href="php/logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
    </nav>

    <main class="main-content" id="main-content">
        <div class="content-wrapper">
            <h1>Resultados de la Verificación de Pagos</h1>
            
            <?php if ($status === 'success'): ?>
                <div class="alert alert-success" style="font-size: 1.1rem;"><?= htmlspecialchars($mensaje) ?></div>
            <?php else: ?>
                <div class="alert alert-error">Error: <?= htmlspecialchars($mensaje) ?></div>
            <?php endif; ?>

            <?php if ($url_descarga): ?>
                <div style="margin-top: 2rem; text-align: center;">
                    <a href="<?= htmlspecialchars($url_descarga) ?>" class="btn btn-primary" download style="width: auto; display: inline-block; font-size: 1.2rem; padding: 1rem 2rem;">
                        <i class="fas fa-file-excel"></i> Descargar Reporte (Mal_Pagadas_ccz)
                    </a>
                </div>
            <?php endif; ?>

            <a href="principal.php" class="btn" style="margin-top: 3rem; width: auto; display: inline-block; background-color: #6c757d; color: white;">Volver al Menú</a>
        </div>
    </main>

    <script>
        document.getElementById('menu-toggle').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>