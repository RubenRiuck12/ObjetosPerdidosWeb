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
    $sql_objects = "SELECT Foto FROM Objetos WHERE Usuarios_IDUsuario = ?";
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
            echo "<script>alert('Datos actualizados correctamente.');</script>";
            // Recargar los datos actualizados
            $user['Nombre'] = $nombre;
            $user['Email'] = $email;
            $user['CURP'] = $curp;
            $user['NumTelefono'] = $numTelefono;
        } else {
            echo "<script>alert('Error al actualizar los datos: " . htmlspecialchars($conn->error) . "');</script>";
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
    <link rel="stylesheet" href="PerfilAdministrador.css">
</head>
<body>
    <div id="app" class="perfil">
    <header class="header">
            <div class="logo">
            <a href="PaginaPrincipalAdmin.php">
                <img src="Imagenes/logobuscatec-modified.png" alt="Logo">
            </a>
            </div>
            <img src="Imagenes/3.png" alt="Usuario" class="logoescuela">
            <div class="profile">
            <button class="logout-btn" onclick="location.href='Logout.php'">Cerrar sesión</button>
            </div>
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
            <script>
                document.getElementById("changeButton").addEventListener("click", function() {
                    window.location.href = "PerfilAdminContraC.html";
                });
            </script>
        </div>
    </div>
    <script src="PerfilAdministradorEditar.js"></script>
</body>
</html>