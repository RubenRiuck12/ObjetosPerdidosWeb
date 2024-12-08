<?php 
session_start();
include 'Conexion.php';

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Consulta para obtener los datos del usuario
    $sql = "SELECT NumTelefono, Nombre, CURP, IDUsuario, Email FROM Usuarios WHERE Nombre = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica si se encontró el usuario
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "Usuario no encontrado.";
        exit;
    }

    $userId = $user['IDUsuario'];

    // Consulta para obtener los objetos asociados al usuario
    $sql_objects = "SELECT Foto, IDObjeto FROM Objetos WHERE Usuarios_IDUsuario = ?";
    $stmt_objects = $conn->prepare($sql_objects);
    $stmt_objects->bind_param("i", $userId);
    $stmt_objects->execute();
    $result_objects = $stmt_objects->get_result();    

    // Verificar si se ha enviado una actualización
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $curp = $_POST['curp'];
        $numTelefono = $_POST['telefono'];

        // Actualizar los datos en la base de datos
        $update_sql = "UPDATE Usuarios SET Nombre = ?, Email = ?, CURP = ?, NumTelefono = ? WHERE Nombre = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssss", $nombre, $email, $curp, $numTelefono, $username);

        if ($update_stmt->execute()) {
            echo "Datos actualizados correctamente.";
            // Recargar los datos actualizados
            $user['Nombre'] = $nombre;
            $user['Email'] = $email;
            $user['CURP'] = $curp;
            $user['NumTelefono'] = $numTelefono;
        } else {
            echo "Error al actualizar los datos: " . $conn->error;
        }

        $update_stmt->close();
    }

    $stmt->close();
    $stmt_objects->close();
    $conn->close();
} else {
    echo "No has iniciado sesión.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="PerfilUser.css">
</head>
<body>
    <div id="app" class="perfil">
        <header class="header">
            <div class="logo">
                <a href="PaginaPrincipalUser.php">
                    <img src="Imagenes/logobuscatec-modified.png" alt="Logo">
                </a>
            </div>
            <button class="logout-btn" onclick="location.href='Logout.php'">Cerrar sesión</button>
        </header>

        <div class="container">
            <section class="profile-section">
                <div class="left-section">
                    <h2>Perfil</h2>
                    <div class="profile-image">
                        <img src="Imagenes/IconoUsuario.png" alt="Foto de perfil">
                        <input type="file" accept="image/*" id="fileInput" style="display: none;" onchange="handleFileUpload(event)">
                        <button id="editButton" class="upload-btn">Editar</button>
                        <button id="saveButton" class="upload-btn" style="display:none;">Guardar cambios</button>
                        <button id="changeButton" class="upload-btn" style="display:none;">Cambiar Contraseña</button>
                    </div>
                </div>
                <div class="right-section">
                    <form class="profile-form" id="profileForm" method="POST">
                        <div class="form-group">
                            <label for="nombre">Nombre(s):</label>
                            <input type="text" id="nombre" name="nombre" class="input-field" value="<?php echo htmlspecialchars($user['Nombre']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="text" id="email" name="email" class="input-field" value="<?php echo htmlspecialchars($user['Email']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="curp">CURP:</label>
                            <input type="text" id="curp" name="curp" class="input-field" value="<?php echo htmlspecialchars($user['CURP']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono:</label>
                            <input type="text" id="telefono" name="telefono" class="input-field" value="<?php echo htmlspecialchars($user['NumTelefono']); ?>" disabled>
                        </div>
                    </form>
                </div>
            </section>

            <section class="objects-section">
                <h2>Mis objetos</h2>
                <div class="objects">
                <?php
                    if ($result_objects->num_rows > 0) {
                        while ($object = $result_objects->fetch_assoc()) {
                            $foto = 'data:image/jpeg;base64,' . base64_encode($object['Foto']);
                            $objectId = $object['IDObjeto']; // ID del objeto
                            echo '<div class="object-item">';
                            echo '<img src="' . $foto . '" alt="Objeto">';
                            echo '<form method="POST" action="eliminar_objeto.php" onsubmit="return confirmDelete();">';
                            echo '<input type="hidden" name="objectId" value="' . $objectId . '">';
                            echo '<button type="submit" class="delete-btn">Eliminar</button>';
                            echo '</form>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No tienes objetos registrados.</p>';
                    }
                ?>
                </div>
            </section>
            <script>
                document.getElementById("changeButton").addEventListener("click", function() {
                    window.location.href = "PerfilUserContraC.html";
                });

                function confirmDelete() {
                    var confirmation = confirm("¿Estás seguro de que deseas eliminar el objeto?");
                    if (confirmation) {
                        alert("Objeto eliminado.");
                        return true;
                    } else {
                        return false;
                    }
                }
            </script>
        </div>
    </div>
    <script src="PerfilUserEditar.js"></script>
</body>
</html>