<?php
require_once 'php/verificar_sesion.php';

// Verificar si hay resultados en la sesión
if (!isset($_SESSION['resultado_reporte'])) {
    // Si no hay, redirigir a la página principal
    header('Location: principal.php');
    exit();
}

// Obtener los datos y luego limpiarlos de la sesión
$resultado_json = $_SESSION['resultado_reporte'];
$nombres_archivos = $_SESSION['nombres_archivos'];
unset($_SESSION['resultado_reporte'], $_SESSION['nombres_archivos']);

$datos = json_decode($resultado_json, true);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados del Reporte - Orinoco Proto</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .status-ok { color: #2e7d32; font-weight: bold; }
        .status-error { color: #c62828; font-weight: bold; }
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
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
        <div class="content-wrapper">
            <h1>Resultados de Conciliación AT48 vs. D01</h1>
            <p><strong>Archivos Analizados:</strong> 
                <span style="color:var(--color-azul-rey);"><?= htmlspecialchars($nombres_archivos['at48']) ?></span> vs 
                <span style="color:var(--color-azul-rey);"><?= htmlspecialchars($nombres_archivos['d01']) ?></span>
            </p>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th>Ops AT48</th>
                            <th>Ops D01</th>
                            <th>Observación Ops</th>
                            <th>Monto $ AT48</th>
                            <th>Monto $ D01</th>
                            <th>Monto Bs AT48</th>
                            <th>Monto Bs D01</th>
                            <th>Status General</th>
                        </tr>
                    </thead>
                        <tbody>
                            <?php if (isset($datos[0]['error'])): ?>
                                <tr><td colspan="9" class="status-error">Error al procesar los archivos: <?= htmlspecialchars($datos[0]['error']) ?></td></tr>
                            <?php else: ?>
                                <?php foreach ($datos as $fila): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($fila['Servicio']) ?></td>
                                        <td><?= $fila['Cant_Ops_AT48'] ?></td>
                                        <td><?= $fila['Cant_Ops_D01'] ?></td>
                                        <td class="<?= $fila['Diff_Ops'] != 0 ? 'status-error' : 'status-ok' ?>"><?= htmlspecialchars($fila['Observacion_Ops']) ?></td>
                                        
                                        <td><?= number_format($fila['Monto_USD_AT48'], 2, ',', '.') ?></td>
                                        <td><?= number_format($fila['Monto_USD_D01'], 2, ',', '.') ?></td>
                                        <td><?= number_format($fila['Monto_Bs_AT48'], 2, ',', '.') ?></td>
                                        <td><?= number_format($fila['Monto_Bs_D01'], 2, ',', '.') ?></td>
                                        <td class="<?= $fila['Status'] == '✅ Correcto' ? 'status-ok' : 'status-error' ?>"><?= $fila['Status'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                </table>
            </div>
            <a href="principal.php" class="btn btn-primary" style="margin-top: 2rem; width: auto; display: inline-block;">Volver al Menú</a>
        </div>
    </main>

    <script>
        document.getElementById('menu-toggle').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>