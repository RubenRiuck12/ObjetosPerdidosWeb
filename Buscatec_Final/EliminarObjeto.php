<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

include('Conexion.php'); 

$mensajeSuccess = "";
$mensajeError = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Si se presionó el botón 'Buscar'
    $idObjeto = $_POST['idObjeto'];

    // Validación del ID
    if (!empty($idObjeto)) {
        // Consulta para verificar si el objeto existe
        $query = "SELECT o.IDObjeto, o.Clasificacion, o.Estado, o.Fecha, o.Descripción, o.Contacto, o.Usuarios_IDUsuario
                  FROM Objetos o
                  WHERE o.IDObjeto = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $idObjeto);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // El objeto existe, lo mostramos en la tabla
            $objeto = $result->fetch_assoc();
        } else {
            $mensajeError = "El objeto no existe.";
        }
    }

    // Si se presionó el botón 'Eliminar'
    if (isset($_POST['eliminar'])) {
        $queryDelete = "DELETE FROM Objetos WHERE IDObjeto = ?";
        $stmtDelete = $conn->prepare($queryDelete);
        $stmtDelete->bind_param('i', $idObjeto);
        $stmtDelete->execute();

        // Mensaje de éxito para eliminación y recarga de la página con JavaScript
        $mensajeSuccess = "El objeto ha sido eliminado con éxito.";
        echo "<script>
                alert('$mensajeSuccess');
                window.location.href = '" . $_SERVER['PHP_SELF'] . "';
              </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar objeto</title>
    <link rel="stylesheet" href="EliminarObjeto.css">
    <script>
        function confirmarEliminacion() {
            return confirm("¿Estás seguro de que deseas eliminar este objeto?");
        }
    </script>
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

        <div class="contenido">
            <h2>Eliminar Objeto</h2>

            <!-- Formulario de búsqueda por ID -->
            <form method="POST" action="">
                <label for="idObjeto">ID del Objeto:</label>
                <input type="text" id="idObjeto" name="idObjeto" required>
                <button type="submit">Buscar</button>
            </form>

            <!-- Mostrar mensajes de error -->
            <?php if (!empty($mensajeError)) { echo "<p class='error'>$mensajeError</p>"; } ?>

            <!-- Mostrar la tabla si el objeto existe -->
            <?php if (isset($objeto)) { ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Clasificación</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Descripción</th>
                        <th>Usuario (ID)</th>
                        <th>Acción</th>
                    </tr>
                    <tr>
                        <td><?php echo $objeto['IDObjeto']; ?></td>
                        <td>
                            <?php
                            $clasificacion = ['1' => 'Ropa', '2' => 'Dinero', '3' => 'Útiles Escolares', '4' => 'Tecnología', '5' => 'Joyería', '6' => 'Kpop', '7' => 'Otros'];
                            echo $clasificacion[$objeto['Clasificacion']];
                            ?>
                        </td>
                        <td><?php echo $objeto['Estado'] == 1 ? 'Encontrado' : 'Perdido'; ?></td>
                        <td><?php echo $objeto['Fecha']; ?></td>
                        <td><?php echo $objeto['Descripción']; ?></td>
                        <td><?php echo $objeto['Usuarios_IDUsuario']; ?></td>
                        <td>
                            <form method="POST" action="" onsubmit="return confirmarEliminacion();">
                                <input type="hidden" name="idObjeto" value="<?php echo $objeto['IDObjeto']; ?>">
                                <button type="submit" name="eliminar">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                </table>
            <?php } ?>
        </div>
    </div>
</body>
</html>
