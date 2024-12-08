<?php
session_start();
include 'Conexion.php'; // Incluye el archivo de conexión

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldPassword = $_POST['contraseñaold'];
    $newPassword = $_POST['contraseñanew'];

    // Validar que los campos no estén vacíos
    if (empty($oldPassword) || empty($newPassword)) {
        die("Todos los campos son obligatorios.");
    }

    // Escapar las entradas para prevenir inyección SQL
    $oldPassword = $conn->real_escape_string($oldPassword);
    $newPassword = $conn->real_escape_string($newPassword);

    // Consulta para verificar la contraseña anterior
    $userId = $_SESSION['username']; // Reemplaza con el ID del usuario autenticado
    $sqlCheck = "SELECT IDUsuario FROM Usuarios WHERE Nombre = '$userId' AND Contraseña = '$oldPassword'";

    $result = $conn->query($sqlCheck);

    if ($result->num_rows > 0) {
        // Actualizar la contraseña si existe
        $sqlUpdate = "UPDATE Usuarios SET Contraseña = '$newPassword' WHERE Nombre = '$userId'";
        if ($conn->query($sqlUpdate) === TRUE) {
            header('Location: PerfilUserEditar.php');
            exit();
        } else {
            header('Location: PerfilUserContraC.html');
            exit();
        }
    } else {
        header('Location: PerfilUserContraC.html');
        exit();
    }
}

$conn->close();
?>