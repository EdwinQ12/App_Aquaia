<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquaia - Registro</title>

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
                <li class="nav-item"><a class="nav-link text-white" href="iniciar_sesion.php">Inicio de Sesion</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#descarga">Ayuda</a></li>
            </ul>
        </nav>
    </header>

    <div class="content">
        <section id="inicio">
            <h2>Bienvenido a Aquaia</h2>
            <p>Unete a nosotros y difruta de todos los veneficios de Aquaia</p>
        </section>

        <section id="contacto">
            <h2>Formulario de Registro</h2>
            <form action="registrar.php" id="form_registro" method="POST">
                <div class="mb-3">
                    <input type="text" id="nombre" class="form-control" name="nombre" placeholder="Tu Nombre" required>
                </div>
                <div class="mb-3">
                    <input type="email" id="email" name="email" class="form-control" placeholder="Tu Email" required>
                </div>
                <div class="mb-3">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Tu Contraseña" required>
                </div>
                <button type="submit" class="btn btn-success" id="registrar" name="registrar">Registrar</button>
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
require 'vendor/autoload.php'; // Cargar Composer

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Firestore;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Conectar a Firebase
$factory = (new Factory)->withServiceAccount('firebase_credentials.json');
$auth = $factory->createAuth();
$firestore = $factory->createFirestore();

// Accede a Firestore a través de su cliente
$firestoreDatabase = $firestore->database();

// Acceder a la colección "usuarios"
$usersCollection = $firestoreDatabase->collection('usuarios');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : "";
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : "";
    $role = isset($_POST["role"]) ? $_POST["role"] : "user"; 

    // Validaciones
    if ($nombre === "" || $email === "" || $password === "") {
        echo "<script>alert('Todos los campos son obligatorios'); window.history.back();</script>";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Correo electrónico inválido'); window.history.back();</script>";
        exit();
    }

    // Comprobar si el correo ya está registrado en Firebase
    try {
        $auth->getUserByEmail($email);
        echo "<script>alert('Este correo ya está registrado'); window.history.back();</script>";
        exit();
    } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
        // El correo no está registrado, podemos proceder
    }

    // Crear usuario en Firebase Authentication
    $userProperties = [
        'email' => $email,
        'emailVerified' => false,
        'password' => $password,
        'displayName' => $nombre,
        'disabled' => false,
    ];
    $createdUser = $auth->createUser($userProperties);
    $uid = $createdUser->uid;

    // Guardar datos del usuario en Firestore
    $usersCollection->document($uid)->set([
        'nombre' => $nombre,
        'email' => $email,
        'role' => $role,
        'password' => password_hash($password, PASSWORD_BCRYPT), // Encriptar la contraseña
    ]);

    // Configuración de PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'bnjm1028@gmail.com'; 
        $mail->Password = 'jfoa oiwm wybv pzxt'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Configurar remitente y destinatario
        $mail->setFrom('no-reply@aquaia.com', 'Aquaia');
        $mail->addAddress($email, $nombre);

        // Configurar formato HTML del correo
        $mail->isHTML(true);
        $mail->Subject = "¡Bienvenido a Aquaia, $nombre!";

        $mail->Body = "
            <div style='font-family: Arial, sans-serif; text-align: center;'>
                <h1 style='color: #00FF6AC2;'>Bienvenida</h1>
                <h2 style='color: #007BFF;'>🎉 ¡Hola, $nombre! 🎉</h2>
                <p style='font-size: 16px; color: #333;'>Tu cuenta ha sido registrada exitosamente.</p>
                <p><strong>📛 Nombre:</strong> $nombre</p>
                <p><strong>📧 Correo:</strong> $email</p>
                <p><strong>🔑 Contraseña:</strong> $password</p>
                <p style='color: red;'>⚠️ Por favor, cambia tu contraseña después de iniciar sesión.</p>
                <a href='https://www.tusitio.com/iniciar_sesion.php' 
                    style='display: inline-block; padding: 10px 20px; background: #28a745; color: #fff; text-decoration: none; border-radius: 5px;'>
                    🔓 Iniciar Sesión
                </a>
            </div>
        ";

        // Enviar correo
        $mail->send();
        echo "<script>alert('Registro exitoso. Se ha enviado la contraseña a tu correo.'); window.location.href='iniciar_sesion.php';</script>";

    } catch (Exception $e) {
        echo "<script>alert('Error al enviar el correo: {$mail->ErrorInfo}'); window.history.back();</script>";
    }
}
?>

