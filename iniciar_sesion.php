<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquaia - Inicio de Sesion</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Video de fondo -->
    <video autoplay muted loop class="video-bg">
        <source src="videos/video_fondo.mp4" type="video/mp4">
        Tu navegador no soporta videos HTML5.
    </video>    

    <header class="text-center">
        <h1>Aquaia</h1>
        <nav>
            <ul class="nav justify-content-center">
                <li class="nav-item"><a class="nav-link text-white" href="principal.html">Pagina Principal</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="registrar.php">Registro</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="">Ayuda</a></li>
            </ul>
        </nav>
    </header>

    <div class="content">
        <section id="inicio">
            <h2>Bienvenido a Aquaia</h2>
        </section>


        <section id="contacto">
            <h2>Formulario de inicio de sesion</h2>
            <form id="form_registro" method="post">
                <div class="mb-3">
                    <input type="email" name="email"id="email" class="form-control" placeholder="Tu Email" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password"id="password" class="form-control" placeholder="Tu Contraseña" required>
                </div>
                <button type="submit" class="btn btn-success" id="iniciar" name="iniciar">iniciar Sesion</button>
            </form>
        </section>
    </div>

    <footer class="text-center">
        <p>&copy; Aquaia. Todos los derechos reservados.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


<?php
session_start();
require "db_conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : "";

    // Validar que los campos no estén vacíos
    if (empty($email) || empty($password)) {
        echo "<script>alert('Todos los campos son obligatorios'); window.location.href = 'iniciar_sesion.php';</script>";
        exit();
    }

    // Validar el formato del correo electrónico
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('El formato del correo electrónico es inválido'); window.location.href = 'iniciar_sesion.php';</script>";
        exit();
    }

    // Consultar el usuario por su email
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario existe y la contraseña es correcta
    if ($usuario && password_verify($password, $usuario["password"])) {
        // Establecer variables de sesión
        $_SESSION["usuario"] = [
            "id" => $usuario["id"],
            "nombre" => $usuario["nombre"],
            "email" => $usuario["email"],
            "role" => $usuario["role"]
        ];

        // Redirigir dependiendo del rol
        if ($usuario["role"] === "admin") {
            header("Location: admin.php"); // Redirigir al panel de administración
            exit();
        } else {
            header("Location: inicio_usuario.php"); // Redirigir al inicio del usuario
            exit();
        }
    } else {
        // Si las credenciales no son correctas
        echo "<script>alert('Credenciales incorrectas'); window.location.href = 'iniciar_sesion.php';</script>";
        exit();
    }
}
?>