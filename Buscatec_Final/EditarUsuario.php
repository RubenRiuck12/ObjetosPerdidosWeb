<?php
header('Content-Type: text/html; charset=UTF-8');

include('Conexion.php'); 
session_start();

// Expresiones regulares para validaciones
$emailRegex = "/^[\w\.\-]+@[\w\-]+\.[a-zA-Z]{2,7}$/";
$emailTecnmRegex = "/^[\w\.\-]+@veracruz\.tecnm\.mx$/"; // Validación específica para TecNM

$passwordRegex = "/^(?=.*[A-Z])(?=.*\d)(?=.*[-*?!@#$\.])[A-Za-z\d\-*?!@#$\.]{8,}$/";
$nameRegex = "/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/";

// Variables iniciales
$nombre = $curp = $numTelefono = $email = $contraseña = "";
$datosValidos = true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idUsuario = $_POST["IDUsuario"];

    if (isset($_POST["buscar"])) {
        // Validar ID de usuario
        if (is_numeric($idUsuario) && strlen($idUsuario) == 8) {
            // Obtener información de la base de datos
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
    } elseif (isset($_POST["guardar"])) {
        // Validaciones de los campos del formulario
        if (!preg_match($nameRegex, $_POST["Nombre"]) || strlen($_POST["Nombre"]) > 45) {
            echo "<script>alert('El nombre solo debe contener letras, no debe exceder los 45 caracteres y sin números.');</script>";
            $datosValidos = false;
        } elseif (!preg_match($emailRegex, $_POST["Email"]) && !preg_match($emailTecnmRegex, $_POST["Email"]) || strlen($_POST["Email"]) > 76) {
    echo "<script>alert('El correo no es válido o excede los 76 caracteres.');</script>";
    $datosValidos = false;
} elseif (!preg_match($passwordRegex, $_POST["Contraseña"])) {
            echo "<script>alert('La contraseña debe tener al menos una letra mayúscula, un número, un carácter especial (- * ? ! @ # $ .) y no contener espacios en blanco.');</script>";
            $datosValidos = false;
        } elseif (!is_numeric($_POST["NumTelefono"]) || strlen($_POST["NumTelefono"]) != 10) {
            echo "<script>alert('El número de teléfono debe ser numérico y contener exactamente 10 dígitos.');</script>";
            $datosValidos = false;
        }

        // Solo se guarda si todas las validaciones fueron exitosas
        if ($datosValidos) {
            $nombre = strtoupper($_POST["Nombre"]);  // Convertir nombre a mayúsculas
            $curp = $_POST["CURP"];
            $numTelefono = $_POST["NumTelefono"];
            $email = $_POST["Email"];
            $contraseña = $_POST["Contraseña"];
            
            $sql = "UPDATE Usuarios SET Nombre=?, CURP=?, NumTelefono=?, Email=?, Contraseña=? WHERE IDUsuario=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $nombre, $curp, $numTelefono, $email, $contraseña, $idUsuario);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "<script>alert('Información actualizada exitosamente.');</script>";
            } else {
                echo "<script>alert('No se pudo actualizar la información o no hubo cambios.');</script>";
            }
            $stmt->close();
        }
    } elseif (isset($_POST["borrar"])) {
        // Limpiar las variables de los datos del usuario
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
    <title>Editar usuario</title>
    <link rel="stylesheet" href="EditarUsuario.css">
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
        <button class="volver-btn" onclick="window.location.href='DBAdmin.php'">Volver</button>
        
        <div class="content-container">
            <form method="POST" action="EditarUsuario.php">
                <div style="text-align: center;">
                    <label for="IDUsuario">ID de Usuario:</label>
                    <input type="text" name="IDUsuario" id="IDUsuario" required maxlength="8" placeholder="Ingrese el ID del usuario" value="<?php echo isset($_POST['IDUsuario']) ? $_POST['IDUsuario'] : ''; ?>">
                    <button type="submit" name="buscar">Buscar</button>
                    <button type="submit" name="borrar" >Borrar</button> <!-- Botón de Borrar -->
                </div>
                
                <?php if ($nombre || $curp || $numTelefono || $email || $contraseña): ?>
                <div class="user-table">
                    <table>
                        <tr>
                            <th>Nombre</th>
                            <th>CURP</th>
                            <th>Número de Teléfono</th>
                            <th>Email</th>
                            <th>Contraseña</th> <!-- Columna de Contraseña -->
                        </tr>
                        <tr>
                            <td><?php echo htmlspecialchars($nombre); ?></td>
                            <td><?php echo htmlspecialchars($curp); ?></td>
                            <td><?php echo htmlspecialchars($numTelefono); ?></td>
                            <td><?php echo htmlspecialchars($email); ?></td>
                            <td><?php echo htmlspecialchars($contraseña); ?></td> <!-- Mostrar Contraseña -->
                        </tr>
                    </table>
                </div>
                
                <div class="user-edit">
                    <label>Nombre:</label>
                    <input type="text" name="Nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>

                    <label>CURP:</label>
                    <input type="text" name="CURP" value="<?php echo htmlspecialchars($curp); ?>" required>

                    <label>Número de Teléfono:</label>
                    <input type="text" name="NumTelefono" value="<?php echo htmlspecialchars($numTelefono); ?>" required>

                    <label>Email:</label>
                    <input type="email" name="Email" value="<?php echo htmlspecialchars($email); ?>" required>

                    <label>Contraseña:</label>
                    <input type="password" name="Contraseña" value="<?php echo htmlspecialchars($contraseña); ?>" required>

                    <button type="submit" name="guardar">Guardar Cambios</button>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>
</html>
