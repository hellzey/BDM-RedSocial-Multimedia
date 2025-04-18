<?php
$host = "localhost";
$usuario = "root"; // Usuario por defecto de XAMPP
$contrasena = "";  // Contraseña por defecto (vacía)
$base_datos = "bdm"; // Cambia esto por el nombre real de tu base de datos


$conn = new mysqli($host, $usuario, $contrasena, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} 
?>
