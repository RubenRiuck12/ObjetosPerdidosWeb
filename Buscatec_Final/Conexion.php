<?php
$servername = "localhost"; // Cambia a tu servidor de base de datos
$username = "Ruben_A"; // Cambia a tu usuario de base de datos
$password = "Mordecai10"; // Cambia a tu contraseña de base de datos
$dbname = "proyecto_g"; // Cambia al nombre de tu base de datos
$port = '3307';

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Revisar conexión
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}
?>