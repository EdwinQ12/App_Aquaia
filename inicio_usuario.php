<?php
session_start();
require "db_conexion.php";

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario"])) {
    header("Location: iniciar_sesion.php");
    exit();
}

$usuario = $_SESSION["usuario"];



?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<video autoplay muted loop class="video-bg">
    <source src="videos/video_fondo.mp4" type="video/mp4">
    Tu navegador no soporta videos HTML5.
</video>

<header class="text-center">
    <h1>Aquaia</h1>
    <nav>
        <ul class="nav justify-content-center">
            <li class="nav-item">
                <a class="nav-link text-white" href="#" data-bs-toggle="modal" data-bs-target="#perfilModal">Mi Perfil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="logout.php">Cerrar Sesión</a>
            </li>
        </ul>
    </nav>
</header>

<div class="container mt-5 text-center">
    <h2>Bienvenido, <?php echo htmlspecialchars($usuario["nombre"]); ?>!</h2>
    <p>Email: <?php echo htmlspecialchars($usuario["email"]); ?></p>

    <button class="btn btn-danger" onclick="eliminarCuenta()">Eliminar Cuenta</button>
</div>

<!-- Modal de Perfil -->
<div class="modal fade" id="perfilModal" tabindex="-1" aria-labelledby="perfilModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="perfilModalLabel">Mi Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form action="actualizar_perfil.php" method="POST">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario["nombre"]); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario["email"]); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function eliminarCuenta() {
    if (confirm("¿Estás seguro de que quieres eliminar tu cuenta? Esta acción no se puede deshacer.")) {
        window.location.href = "eliminar_cuenta.php";
    }
}
</script>

</body>
</html>
