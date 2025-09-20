<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Orinoco Proto</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <div class="container">
        <div class="form-container">
            <h2>Acceso a Reportes</h2>

        <?php
        // --- NUEVO BLOQUE PARA MENSAJE DE LOGOUT --- //
        // Verificamos si la URL contiene el parámetro 'logout' con el valor 'success'
        if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
            echo '<div class="alert alert-success">Has cerrado sesión de forma segura. ¡Hasta pronto!</div>';
        }

        // --- BLOQUE ANTIGUO PARA MENSAJE DE ERROR (lo dejamos donde está) --- //
        if (isset($_GET['error']) && $_GET['error'] == '1') {
            // (Este bloque se queda al final del body para el script de la alerta)
        }
        ?>

    <form action="php/login_proceso.php" method="POST">     
                <div class="form-group">
                    <label for="id">Cédula de Identidad</label>
                    <input type="number" id="id" name="id" class="input-field" placeholder="Ingrese su cédula" required>
                </div>

                <div class="form-group">
                    <label for="contrasena">Contraseña</label>
                    <input type="password" id="contrasena" name="contrasena" class="input-field" placeholder="Ingrese su contraseña" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Ingresar</button>
            </form>
        </div>
    </div>

    <?php
    // --- Lógica para la Ventana Emergente de Error --- //
    // CORREGIDO: Este bloque ahora está DENTRO del body, justo antes de que se cierre.
    if (isset($_GET['error']) && $_GET['error'] == '1') {
        echo "<script>alert('ID o contraseña incorrecta. Por favor, intente de nuevo.');</script>";
    }
    ?>

</body>
</html>