<?php
session_start();
include 'Conexion.php';

// Verifica si se ha enviado la solicitud para eliminar un objeto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['objectId'])) {
    $objectId = $_POST['objectId'];

    // Consulta para eliminar el objeto de la base de datos
    $delete_sql = "DELETE FROM Objetos WHERE IDObjeto = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $objectId);

    if ($stmt->execute()) {
        echo "<script>alert('Objeto eliminado'); window.location.href='PerfilUserEditar.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar el objeto'); window.location.href='PerfilUserEditar.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
