<?php
$password = 'zefiro26';  // La contraseña en texto plano
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
echo $hashedPassword;  // Esto te mostrará la contraseña encriptada
?>
