<?php
session_start();
set_time_limit(300);
// CORRECCIÓN: La ruta ahora es correcta para un archivo dentro de /php
require_once 'verificar_sesion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['archivo_d01']) && $_FILES['archivo_d01']['error'] === UPLOAD_ERR_OK) {
        
        $uploads_dir = '../uploads/';
        
        $d01_name = session_id() . '_' . time() . '_' . basename($_FILES['archivo_d01']['name']);
        $d01_path = $uploads_dir . $d01_name;
        move_uploaded_file($_FILES['archivo_d01']['tmp_name'], $d01_path);
        $d01_safe_path = escapeshellarg($d01_path);

        $freshdesk_safe_path = "None";
        if (isset($_FILES['archivo_freshdesk']) && $_FILES['archivo_freshdesk']['error'] === UPLOAD_ERR_OK) {
            $freshdesk_name = session_id() . '_' . time() . '_' . basename($_FILES['archivo_freshdesk']['name']);
            $freshdesk_path = $uploads_dir . $freshdesk_name;
            move_uploaded_file($_FILES['archivo_freshdesk']['tmp_name'], $freshdesk_path);
            $freshdesk_safe_path = escapeshellarg($freshdesk_path);
        }

        // CORRECCIÓN: Se usa la ruta absoluta a Python para máxima fiabilidad
        $ruta_python = '"C:\\Users\\Admin\\AppData\\Local\\Programs\\Python\\Python313\\python.exe"';
        
        $command = $ruta_python . " ../python_scripts/analisis_reembolsos.py $d01_safe_path $freshdesk_safe_path " . escapeshellarg(session_id());
        $resultado_json = shell_exec($command . " 2>&1");

        $_SESSION['resultado_reporte'] = $resultado_json;
        
        // La ruta de redirección aquí es correcta
        header('Location: ../resultados_reembolsos.php');
        exit();
    }
}
header('Location: ../reporte_reembolsos.php?error=upload');
exit();
?>