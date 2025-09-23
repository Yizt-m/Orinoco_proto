<?php
// CORRECCIÓN: La ruta ahora es correcta para un archivo en la raíz
require_once 'php/verificar_sesion.php';

if (!isset($_SESSION['resultado_reporte'])) {
    // CORRECCIÓN: Se añade ../ para subir un nivel antes de redirigir
    // Aunque en este archivo no es estrictamente necesario, es una buena práctica ser consistente.
    // La verdadera corrección es para el caso de que un script en /php redirija.
    // El problema principal era que el script anterior fallaba.
    header('Location: principal.php');
    exit();
}

$resultado_json = $_SESSION['resultado_reporte'];
unset($_SESSION['resultado_reporte']);

$datos_completos = json_decode($resultado_json, true);
$tabla_final = $datos_completos['tabla_final'] ?? [];
$error = $datos_completos['error'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados del Reporte de Reembolsos</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        table { width: 100%; max-width: 800px; margin: 2rem auto; border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: var(--color-azul-rey); color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        tr:last-child { font-weight: bold; background-color: #e0e0e0; }
        td:nth-child(2), td:nth-child(3) { text-align: right; }
    </style>
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
        <div class="content-wrapper" style="text-align: center;">
            <h1>Reporte de Reembolsos por País</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error">Error: <?= htmlspecialchars($error) ?></div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>País</th>
                            <th>Reembolsos</th>
                            <th>Monto de Reembolsos (USD)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tabla_final as $fila): ?>
                            <tr>
                                <td><?= htmlspecialchars($fila['Pais']) ?></td>
                                <td><?= number_format($fila['Reembolsos']) ?></td>
                                <td><?= number_format($fila['Monto_Reembolsos'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <a href="principal.php" class="btn btn-primary" style="margin-top: 2rem; width: auto; display: inline-block;">Volver al Menú</a>
        </div>
    </main>
    
    <?php
        require_once 'php/conexion.php';
        $session_id = session_id();
        $stmt = $conexion->prepare("DELETE FROM registros_envios WHERE user_session_id = ?");
        $stmt->bind_param("s", $session_id);
        $stmt->execute();
        $stmt->close();
        $conexion->close();
    ?>

    <script>
        document.getElementById('menu-toggle').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>