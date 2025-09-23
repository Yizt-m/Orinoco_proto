<!DOCTYPE html>
<html lang="es">
<head>
    <title>Prueba de Ejecución de Python</title>
    <style>body { font-family: monospace; background-color: #f4f4f4; padding: 2rem; }</style>
</head>
<body>
    <h1>Probando la conexión PHP -> Python</h1>
    <pre>
<?php
// Usa la misma ruta absoluta que definiste antes
$ruta_python = '"C:\\Users\\Admin\\AppData\\Local\\Programs\\Python\\Python313\\python.exe"';

// El comando más simple: pedir la versión de Python
$command = $ruta_python . " --version 2>&1";

echo "<b>Ejecutando el comando:</b>\n" . htmlspecialchars($command) . "\n\n";

// Ejecutamos el comando
$output = shell_exec($command);

echo "<b>Resultado:</b>\n";
var_dump($output);
?>
    </pre>
</body>
</html>