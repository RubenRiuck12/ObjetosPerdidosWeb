<?php
include 'Conexion.php';

// Recibir datos del formulario
    $nombre = $_POST['nombre'];
    $email = $_POST['correo'];
    $curp = $_POST['curp'];
    $telefono = $_POST['telefono'];
    $password = $_POST['password'];
    $idusuario = $_POST['id'];
    $apellido = $_POST['apellido'];

    $rol = 1;

    // Crear consulta SQL
    $sql = "INSERT INTO usuarios (IDUsuario, Nombre, CURP, NumTelefono, Email, Rol, Contrasena) 
            VALUES ('$idusuario', CONCAT('$nombre',' ' , '$apellido' ), '$curp', '$telefono', '$email', '$rol', '$password')";

    // Ejecutar consulta y verificar
    if ($conn->query($sql) === TRUE) {
        header("Location: index.html");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }

// Cerrar conexión
$conn->close();
?>