<?php
header('Content-Type: text/html; charset=UTF-8');

include('Conexion.php'); 
session_start();

// Inicializar variables para evitar errores
$idUsuario = $nombre = $apellido = $curp = $telefono = $email = $rol = $contraseña = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idUsuario = $_POST['IDUsuario'];
    $nombre = strtoupper($_POST['nombre']); // Convertir nombre a mayúsculas
    $apellido = strtoupper($_POST['apellido']); 
    $curp = $_POST['curp'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];
    $contraseña = $_POST['contraseña'];
    
    // Concatenar nombre y apellido
    $nombreCompleto = $nombre . " " . $apellido;

    // Verificar duplicados en IDUsuario, CURP, teléfono y correo
    $sql = "SELECT * FROM Usuarios WHERE IDUsuario = '$idUsuario' OR CURP = '$curp' OR NumTelefono = '$telefono' OR Email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Obtener los valores duplicados encontrados
        $row = $result->fetch_assoc();
        $mensajeError = "";

        if ($row['IDUsuario'] === $idUsuario) {
            $mensajeError .= "El ID de usuario ya existe. ";
        }
        if ($row['CURP'] === $curp) {
            $mensajeError .= "La CURP ya está registrada. ";
        }
        if ($row['NumTelefono'] === $telefono) {
            $mensajeError .= "El número de teléfono ya está registrado. ";
        }
        if ($row['Email'] === $email) {
            $mensajeError .= "El correo electrónico ya está registrado. ";
        }

        echo "<script>alert('$mensajeError'); window.location.href='AgregarUsuario.php';</script>";
        exit();  // Detener el script después de la redirección
    } else {
        // Insertar el nuevo usuario con el nombre completo
        $sql = "INSERT INTO Usuarios (IDUsuario, Nombre, CURP, NumTelefono, Email, Rol, Contraseña)
                VALUES ('$idUsuario', '$nombreCompleto', '$curp', '$telefono', '$email', '$rol', '$contraseña')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Usuario agregado exitosamente'); window.location.href='AgregarUsuario.php';</script>";
            exit();  // Detener el script después de la redirección
        } else {
            echo "<script>alert('Error al agregar el usuario: " . $conn->error . "'); window.location.href='AgregarUsuario.php';</script>";
            exit();  // Detener el script después de la redirección
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar usuario</title>
    <link rel="stylesheet" href="AgregarUsuario.css">
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
                <img src="Imagenes/IconoUsuario.png" alt="Usuario" class="perfilicon">
                <button class="logout-btn" onclick="location.href='Logout.php'">Cerrar sesión</button>
            </div>
        </header>
        
        <button class="volver-btn" onclick="window.location.href='DBAdmin.php'">Volver</button>

        <div class="content-container">
            <form action="AgregarUsuario.php" method="post" onsubmit="return validarFormulario()">
                <h2>Agregar Usuario</h2>
                
                <div class="form-container">
                    <div>
                        <label for="idUsuario">ID del usuario</label>
                        <input type="text" id="idUsuario" name="IDUsuario" required maxlength="8" pattern="\d{8}" title="El ID debe tener 8 dígitos numéricos" placeholder="8 números enteros únicos">
                    </div>

                    <div>
                        <label for="curp">CURP</label>
                        <input type="text" id="curp" name="curp" required maxlength="18" pattern="[A-Z]{4}\d{6}[A-Z]{6}\d{2}" title="La CURP debe tener 18 caracteres en el formato correcto (4 letras, 6 números, 6 letras, 2 números)" placeholder="Inserte la CURP del usuario">
                    </div>
                    <div>
                        <label for="nombre">Nombre(s)</label>
                        <input type="text" id="nombre" name="nombre" required maxlength="22" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" placeholder="Ingrese el nombre(s)">
                    </div>

                    <div>
                        <label for="apellido">Apellido(s)</label>
                        <input type="text" id="apellido" name="apellido" required maxlength="23" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" placeholder="Ingrese los apellidos">
                    </div>

                    <div>
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" required pattern="^[\w\.-]+@[a-zA-Z\d\.-]+\.[a-zA-Z]{2,}$" title="Ingrese un correo electrónico válido" placeholder="Ingrese el correo electrónico">

                    </div>

                    <div>
                        <label for="telefono">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" required maxlength="10" pattern="\d{10}" title="El teléfono debe tener 10 dígitos numéricos" placeholder="Ingrese el número telefónico (10 números)">
                    </div>

                    <div>
                        <label for="rol">Rol</label>
                        <select id="rol" name="rol" required>
                            <option value="2">Administrador</option>
                            <option value="3">DBA</option>
                        </select>
                    </div>

                    <div>
                        <label for="contraseña">Contraseña</label>
                        <input type="password" id="contraseña" name="contraseña" required pattern="^(?=.[A-Z])(?=.\d)(?=.[-?!Q#$\.])[A-Za-z\d-*?!Q#$\.]{8,}$" title="La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un carácter especial (- * ? ! Q # $ .)" placeholder="Ingrese una contraseña adecuada">
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit">Guardar</button>
                    <button type="reset">Limpiar</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>