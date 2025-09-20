<?php
session_start();
require_once 'verificar_sesion.php'; // Seguridad primero

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar que ambos archivos se hayan subido sin errores
    if (isset($_FILES['archivo_at48']) && $_FILES['archivo_at48']['error'] === UPLOAD_ERR_OK &&
        isset($_FILES['archivo_d01']) && $_FILES['archivo_d01']['error'] === UPLOAD_ERR_OK) {

        $uploads_dir = '../uploads/';
        
        // Crear nombres de archivo únicos para evitar colisiones
        $at48_name = session_id() . '_' . time() . '_' . basename($_FILES['archivo_at48']['name']);
        $d01_name = session_id() . '_' . time() . '_' . basename($_FILES['archivo_d01']['name']);

        $at48_path = $uploads_dir . $at48_name;
        $d01_path = $uploads_dir . $d01_name;

        // Mover archivos a la carpeta /uploads
        if (move_uploaded_file($_FILES['archivo_at48']['tmp_name'], $at48_path) &&
            move_uploaded_file($_FILES['archivo_d01']['tmp_name'], $d01_path)) {

            // Escapar argumentos para seguridad
            $at48_safe_path = escapeshellarg($at48_path);
            $d01_safe_path = escapeshellarg($d01_path);

            // Construir y ejecutar el comando de Python
            $command = "python ../python_scripts/analisis_at48.py $at48_safe_path $d01_safe_path";
            // ... línea anterior
            $resultado_json = shell_exec($command);

            $_SESSION['resultado_reporte'] = $resultado_json;
            // ...

            // Guardar resultados en la sesión para mostrarlos en la siguiente página
            $_SESSION['resultado_reporte'] = $resultado_json;
            $_SESSION['nombres_archivos'] = [
                'at48' => basename($_FILES['archivo_at48']['name']),
                'd01' => basename($_FILES['archivo_d01']['name'])
            ];
            
            // Redirigir a la página de resultados
            header('Location: ../resultados.php');
            exit();
        }
    }
}

// Si algo falla, redirigir de vuelta con un error
header('Location: ../reporte_at48.php?error=upload');
exit();
?>