<?php
include 'Conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idObjeto = $_POST['IDObjeto'];
    $accion = $_POST['accion'];

    if ($accion == 'aprobar') {
        // Cambiar el estado a "Aprobado" (Aprobado = 1)
        $sql = "UPDATE Objetos SET Aprobado = 1 WHERE IDObjeto = $idObjeto";
        if ($conn->query($sql) === TRUE) {
            echo "Objeto aprobado exitosamente.";
        } else {
            echo "Error al aprobar el objeto: " . $conn->error;
        }
    } elseif ($accion == 'rechazar') {
        // Eliminar el objeto si es rechazado
        $sql = "DELETE FROM Objetos WHERE IDObjeto = $idObjeto";
        if ($conn->query($sql) === TRUE) {
            echo "Objeto rechazado y eliminado exitosamente.";
        } else {
            echo "Error al eliminar el objeto: " . $conn->error;
        }
    }
}

$conn->close();

// Redirigir de vuelta a la página de administración
header("Location: PaginaPrincipalAdmin.php");
exit();
?>