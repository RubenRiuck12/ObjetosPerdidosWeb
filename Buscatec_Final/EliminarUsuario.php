<?php
header('Content-Type: text/html; charset=UTF-8');

include('Conexion.php'); 
session_start();

$nombre = $curp = $numTelefono = $email = $contraseña = "";
$idUsuario = isset($_POST["IDUsuario"]) ? $_POST["IDUsuario"] : "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idUsuario = $_POST["IDUsuario"];

    // Buscar usuario
    if (isset($_POST["buscar"])) {
        if (is_numeric($idUsuario) && strlen($idUsuario) == 8) {
            $sql = "SELECT Nombre, CURP, NumTelefono, Email, Contraseña FROM Usuarios WHERE IDUsuario = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $idUsuario);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $nombre = $row["Nombre"];
                $curp = $row["CURP"];
                $numTelefono = $row["NumTelefono"];
                $email = $row["Email"];
                $contraseña = $row["Contraseña"];
            } else {
                echo "<script>alert('Usuario no encontrado.');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('El ID de usuario debe ser numérico y tener 8 dígitos.');</script>";
        }
    }

    // Eliminar usuario
    if (isset($_POST["eliminar"])) {
        if (is_numeric($idUsuario) && strlen($idUsuario) == 8) {
            $sql = "DELETE FROM Usuarios WHERE IDUsuario = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $idUsuario);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "<script>alert('Usuario eliminado exitosamente.');</script>";
                $nombre = $curp = $numTelefono = $email = $contraseña = "";
            } else {
                echo "<script>alert('Error al eliminar el usuario.');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('El ID de usuario debe ser numérico y tener 8 dígitos.');</script>";
        }
    }

    // Limpiar datos
    if (isset($_POST["borrar"])) {
        $idUsuario = $nombre = $curp = $numTelefono = $email = $contraseña = "";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar usuario</title>
    <link rel="stylesheet" href="EliminarUsuario.css">
</head>
<body>
    <div id="app" class="inicioDBA">
        <header class="header">
            <div class="logo">
                <a href="DBAdmin.php">
                    <img src="Imagenes/logobuscatec-modified.png" alt="Logo">
                </a>
            </div>
            <img src="Imagenes/3.png" alt="Usuario" class="logoescuela">
            <div class="profile">
    <div>
    <?php
        // Verifica si el usuario ha iniciado sesión y muestra el nombre
        if (isset($_SESSION['username'])) {
            echo '<a href="PerfilDBAEditar.php" style="text-decoration: none; color: inherit;">' . htmlspecialchars($_SESSION['username']) . '</a>';
        } else {
            echo "Invitado";
        }
        ?>
        <br> 
        <a href="PerfilDBAEditar.php" style="text-decoration: none; color: inherit; font-size: 0.75em;">DBA</a>
    </div>
    <img src="Imagenes/IconoUsuario.png" alt="Usuario" class="perfilicon">
    <button class="logout-btn" onclick="location.href='Logout.php'">Cerrar sesión</button>
</div>
        </header>
        
        <div class="content-container">
            <button class="volver-btn" onclick="window.location.href='DBAdmin.php'">Volver</button>
            
            <form method="POST" action="">
                <label for="IDUsuario">ID Usuario:</label>
                <input type="text" id="IDUsuario" name="IDUsuario" value="<?php echo htmlspecialchars($idUsuario); ?>" required>
                <button type="submit" name="buscar">Buscar</button>
                <button type="submit" name="borrar">Limpiar</button>
                
                <?php if (!empty($nombre)) : ?>
                    <div class="table-container">
                        <table>
                            <tr><th>Nombre</th><td><?php echo htmlspecialchars($nombre); ?></td></tr>
                            <tr><th>CURP</th><td><?php echo htmlspecialchars($curp); ?></td></tr>
                            <tr><th>Teléfono</th><td><?php echo htmlspecialchars($numTelefono); ?></td></tr>
                            <tr><th>Email</th><td><?php echo htmlspecialchars($email); ?></td></tr>
                            <tr><th>Contraseña</th><td><?php echo htmlspecialchars($contraseña); ?></td></tr>
                        </table>
                    </div>
                    <button type="submit" name="eliminar">Eliminar</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>
</html>
