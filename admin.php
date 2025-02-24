<?php
session_start();
require "db_conexion.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Cargar PHPMailer

// Verificar si el usuario ha iniciado sesión y es admin
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["role"] != "admin") {
    header("Location: iniciar_sesion.php");
    exit();
}

// Verificar si se envió el formulario de eliminación
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminar_usuario"])) {
    $usuario_id = $_POST["usuario_id"];

    // Asegurarse de que el usuario existe antes de eliminarlo
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // Eliminar el usuario de la base de datos
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        if ($stmt->execute([$usuario_id])) {
            echo "<script>alert('Usuario eliminado exitosamente.'); window.location.href='admin.php';</script>";
            exit(); // Detener ejecución después de redireccionar
        } else {
            echo "<script>alert('Error al eliminar el usuario.'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('Usuario no encontrado.'); window.history.back();</script>";
        exit();
    }
}

// Obtener la lista de usuarios registrados
$stmt = $pdo->query("SELECT id, nombre, email FROM usuarios");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verificar si el formulario de correo ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["enviar_correo"])) {
    $usuario_id = $_POST["usuario_id"];
    $mensaje = trim($_POST["mensaje"]);

    // Validar que el mensaje no esté vacío
    if (empty($mensaje)) {
        echo "<script>alert('El mensaje no puede estar vacío.'); window.history.back();</script>";
        exit();
    }

    // Obtener el email del usuario
    $stmt = $pdo->prepare("SELECT email, nombre FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // Configuración de PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configuración SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'bnjm1028@gmail.com'; 
            $mail->Password = 'jfoa oiwm wybv pzxt'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Configurar remitente y destinatario
            $mail->setFrom('no-reply@aquaia.com', 'Aquaia Admin');
            $mail->addAddress($usuario["email"], $usuario["nombre"]);

            // Configurar formato HTML del correo
            $mail->isHTML(true);
            $mail->Subject = "Notificacion de Administrador";

            // Contenido del correo en HTML
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; text-align: center;'>
                    <h2 style='color: #007BFF;'>📢 Mensaje del Administrador</h2>
                    <p style='font-size: 16px; color: #333;'>
                        <strong>Estimado/a {$usuario['nombre']},</strong>
                    </p>
                    <p style='font-size: 16px; color: #333;'>{$mensaje}</p>
                    <hr>
                    <p style='font-size: 14px; color: #555;'>Saludos, <br><strong>El equipo de Aquaia</strong></p>
                </div>
            ";

            // Enviar correo
            $mail->send();
            echo "<script>alert('Correo enviado exitosamente a {$usuario['nombre']}'); window.location.href='admin.php';</script>";
            exit();

        } catch (Exception $e) {
            echo "<script>alert('Error al enviar el correo: {$mail->ErrorInfo}'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('Usuario no encontrado.'); window.history.back();</script>";
        exit();
    }
}

// Obtener lista de usuarios para mostrar en la tabla
$stmt = $pdo->query("SELECT id, nombre, email FROM usuarios");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        textarea {
            width: 100%;
            height: 80px;
        }
    </style>
</head>
<body>
    <h1>Usuarios Registrados</h1>

    <table>
        <tr><th>Nombre</th><th>Email</th><th>Enviar Correo</th><th>Eliminar</th></tr>
        <?php foreach ($usuarios as $u) { ?>
            <tr>
                <td><?php echo htmlspecialchars($u['nombre']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td>
                    <form action="admin.php" method="post">
                        <textarea name="mensaje" placeholder="Escribe tu mensaje" required></textarea><br>
                        <input type="hidden" name="usuario_id" value="<?php echo $u['id']; ?>">
                        <button type="submit" name="enviar_correo">Enviar Correo</button>
                    </form>
                </td>
                <td>
                    <form action="admin.php" method="post" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');">
                        <input type="hidden" name="usuario_id" value="<?php echo $u['id']; ?>">
                        <button type="submit" name="eliminar_usuario" style="background-color: red; color: white;">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>

    <a href="logout.php">Cerrar Sesión</a>
</body>
</html>
