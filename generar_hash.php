<?php
// La contraseña que quieres usar
$contrasenaPlana = '123456789';

// Generamos el hash seguro
$hash = password_hash($contrasenaPlana, PASSWORD_DEFAULT);

echo "Copia y pega este HASH en la base de datos: <br><br>";
echo "<strong>" . $hash . "</strong>";
?>