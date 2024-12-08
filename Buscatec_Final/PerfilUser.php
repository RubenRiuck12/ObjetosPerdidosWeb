<?php
session_start();
include 'Conexion.php';

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Consulta para obtener los datos del usuario
    $sql = "SELECT NumTelefono, Nombre, CURP, IDUsuario, Email FROM usuarios WHERE Nombre = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica si se encontró el usuario
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "Usuario no encontrado.";
    }

    $stmt->close();
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
                <img src="Imagenes/logobuscatec-modified.png" class="icon" alt="Logo">
            </div>
            <input type="text" class="search-bar" placeholder="¿Haz perdido algo? Encuéntralo aquí">
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
                    </div>
                </div>
                <div class="right-section">
                    <form class="profile-form" id="profileForm">
                        <div class="form-group">
                            <label for="nombre">Nombre(s):</label>
                            <input type="text" id="nombre" class="input-field" value="<?php echo htmlspecialchars($user['Nombre']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="apellido">Email:</label>
                            <input type="text" id="email" class="input-field" value="<?php echo htmlspecialchars($user['Email']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="curp">CURP:</label>
                            <input type="text" id="curp" class="input-field" value="<?php echo htmlspecialchars($user['CURP']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="num-control">No. De control:</label>
                            <input type="text" id="num-control" class="input-field" value="<?php echo htmlspecialchars($user['IDUsuario']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono:</label>
                            <input type="text" id="telefono" class="input-field" value="<?php echo htmlspecialchars($user['NumTelefono']); ?>" disabled>
                        </div>
                    </form>
                </div>
            </section>

            <section class="objects-section">
                <h2>Mis objetos</h2>
                <div class="objects">
                    <img src="Imagenes/IconoFoto.png" alt="Objeto 1">
                    <img src="Imagenes/IconoFoto.png" alt="Objeto 2">
                </div>
            </section>
        </div>
    </div>
    <script src="PerfilUser.js"></script>
</body>
</html>