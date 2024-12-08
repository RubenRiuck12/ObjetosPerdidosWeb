<?php
include 'Conexion.php';

// Recibir datos del formulario
$clasificacion = $_POST['clasificacion'];
$estado = $_POST['estado'];
$fecha = $_POST['fecha'];
$contacto = $_POST['contacto'];
$descripcion = $_POST['descripcion'];
$nombre_propietario = $_POST['nombre-propietario']; // Usuario que registró el objeto

// Manejo de la imagen
if (isset($_FILES['foto']['tmp_name']) && $_FILES['foto']['tmp_name'] != "") {
    $foto = addslashes(file_get_contents($_FILES['foto']['tmp_name']));
} else {
    $foto = addslashes(file_get_contents('Imagenes/IconoFoto.png'));
}

// Obtener el ID del usuario
$usuario_id = null;
$sql = "SELECT IDUsuario FROM Usuarios WHERE Nombre = '$nombre_propietario' LIMIT 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $usuario_id = $row['IDUsuario'];
}

// Insertar el objeto con Aprobado = 2
if (isset($usuario_id)) {
    $sql = "INSERT INTO Objetos (Clasificacion, Estado, Fecha, Descripcion, Foto, Usuarios_IDUsuario, Archivado, Aprobado, Contacto, Devuelto)
            VALUES ('$clasificacion', '$estado', '$fecha', '$descripcion', '$foto', '$usuario_id', 0, 2, '$contacto', 0)";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: RegistroObjeto.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Error: Usuario no encontrado";
}

$conn->close();
?>