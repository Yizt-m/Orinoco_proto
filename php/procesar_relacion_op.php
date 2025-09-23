<?php
session_start();
require_once 'verificar_sesion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['archivo_p01']) && $_FILES['archivo_p01']['error'] === UPLOAD_ERR_OK &&
        isset($_FILES['archivo_d01']) && $_FILES['archivo_d01']['error'] === UPLOAD_ERR_OK) {

        $uploads_dir = '../uploads/';
        $p01_name = session_id() . '_' . time() . '_' . basename($_FILES['archivo_p01']['name']);
        $d01_name = session_id() . '_' . time() . '_' . basename($_FILES['archivo_d01']['name']);
        $p01_path = $uploads_dir . $p01_name;
        $d01_path = $uploads_dir . $d01_name;

        if (move_uploaded_file($_FILES['archivo_p01']['tmp_name'], $p01_path) &&
            move_uploaded_file($_FILES['archivo_d01']['tmp_name'], $d01_path)) {
            
            // Pasamos las rutas al revés para que coincidan con el orden en Python
            $d01_safe_path = escapeshellarg($d01_path);
            $p01_safe_path = escapeshellarg($p01_path);

            $command = "python ../python_scripts/analisis_relacion_op.py $d01_safe_path $p01_safe_path";
            $resultado_json = shell_exec($command);

            $_SESSION['resultado_reporte'] = $resultado_json;
            
            header('Location: ../resultados_relacion_op.php');
            exit();
        }
    }
}
header('Location: ../reporte_relacion_op.php?error=upload');
exit();
?>