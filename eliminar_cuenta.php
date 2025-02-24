<?php
session_start();
require "db_conexion.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: iniciar_sesion.php");
    exit();
}

$id = $_SESSION["usuario"]["id"];


$stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->execute([$id]);


session_destroy();

echo "<script>alert('Tu cuenta ha sido eliminada'); window.location.href = 'iniciar_sesion.php';</script>";
exit();
?>
