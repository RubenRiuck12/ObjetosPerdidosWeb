<?php
session_start();
include 'Conexion.php'; // Asegúrate de que este archivo tiene la conexión a la base de datos

// Recibir datos del formulario
$usuario = $_POST['usuario'];
$contraseña = $_POST['contraseña'];

// Preparar y ejecutar la consulta SQL para verificar usuario y obtener el rol
$sql = "SELECT Nombre, Rol FROM Usuarios WHERE IDUsuario = ? AND Contraseña = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $usuario, $contraseña);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['username'] = $row['Nombre'];
    $_SESSION['rol'] = $row['Rol']; // Guardar el rol en la sesión

    echo 'success-' . $row['Rol']; // Devuelve el mensaje de éxito con el rol
} else {
    echo 'error'; // Si no se encuentra el usuario
}

$stmt->close();
$conn->close();
?>