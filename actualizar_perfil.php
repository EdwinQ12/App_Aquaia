<?php
session_start();
require "db_conexion.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: iniciar_sesion.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_SESSION["usuario"]["id"];
    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);

    if (empty($nombre) || empty($email)) {
        echo "<script>alert('Todos los campos son obligatorios'); window.location.href = 'inicio_usuario.php';</script>";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Formato de email inválido'); window.location.href = 'inicio_usuario.php';</script>";
        exit();
    }

    $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?");
    $stmt->execute([$nombre, $email, $id]);

    $_SESSION["usuario"]["nombre"] = $nombre;
    $_SESSION["usuario"]["email"] = $email;

    echo "<script>alert('Perfil actualizado correctamente'); window.location.href = 'inicio_usuario.php';</script>";
}
?>
