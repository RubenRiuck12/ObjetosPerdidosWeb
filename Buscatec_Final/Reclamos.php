<?php
session_start(); // Iniciar la sesión al comienzo del script

include 'Conexion.php';

$errorMensaje = "";

// Verificar si hay sesión activa y obtener el IDUsuario basado en el nombre almacenado en la sesión
$idUsuarioSesion = null;
if (isset($_SESSION['username'])) { // Aquí 'username' es el nombre almacenado en la sesión
    $nombreUsuario = $_SESSION['username'];

    // Buscar el IDUsuario en la base de datos usando el nombre
    $sqlBuscarUsuario = "SELECT IDUsuario FROM Usuarios WHERE Nombre = ?";
    $stmtBuscarUsuario = $conn->prepare($sqlBuscarUsuario);
    $stmtBuscarUsuario->bind_param("s", $nombreUsuario);
    $stmtBuscarUsuario->execute();
    $resultadoBuscarUsuario = $stmtBuscarUsuario->get_result();


    if ($resultadoBuscarUsuario->num_rows > 0) {
        $usuarioSesion = $resultadoBuscarUsuario->fetch_assoc();
        $idUsuarioSesion = $usuarioSesion['IDUsuario']; // Extraer el IDUsuario real de la base de datos
    } else {
        $errorMensaje = "El usuario no fue encontrado.";
    }
    $stmtBuscarUsuario->close();
} else {
    $errorMensaje = "Sesión no iniciada.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtén el ID de objeto enviado desde el formulario
    $idObjeto = $_POST['id-objeto'];

    // Verificar si el objeto existe en la base de datos
    $sqlObjetoExiste = "SELECT * FROM Objetos WHERE IDObjeto = ? AND Devuelto = 0";
    $stmtObjetoExiste = $conn->prepare($sqlObjetoExiste);
    $stmtObjetoExiste->bind_param("i", $idObjeto);
    $stmtObjetoExiste->execute();
    $resultadoObjetoExiste = $stmtObjetoExiste->get_result();
    $resultadoObjetoExisteF = $resultadoObjetoExiste->fetch_assoc();
    
    $resultadoObjetoExisteU = $resultadoObjetoExisteF['Usuarios_IDUsuario'];

    if ($resultadoObjetoExiste->num_rows > 0) {
         if( $idUsuarioSesion != $resultadoObjetoExisteU){
    
            // Insertar el reclamo en la tabla `Reclamaciones`
            $fechaEntrega = date("Y-m-d");
            $detallesEntrega = "Reclamo realizado";
    
            $sqlReclamo = "INSERT INTO Reclamaciones (FechaEntrega, DetallesEntrega, Objetos_IDObjeto, Objetos_Usuarios_IDUsuario, Usuarios_IDUsuario) 
                           VALUES (?, ?, ?, ?, ?)";
            $stmtReclamo = $conn->prepare($sqlReclamo);
            $stmtReclamo->bind_param("ssiii", $fechaEntrega, $detallesEntrega, $idObjeto, $resultadoObjetoExisteU, $idUsuarioSesion);
            $stmtReclamo->execute();
    
            $errorMensaje = "El reclamo ha sido registrado correctamente.";
        }else {
            $errorMensaje = "No puedes reclamar un objeto que tu subistes";
        }
    } else {
        $errorMensaje = "Este objeto no está disponible para reclamo.";
    }

    $stmtObjetoExiste->close();
}

// Procesar la cancelación de un reclamo
if (isset($_GET['cancelarReclamo'])) {
    $idReclamoCancelar = $_GET['cancelarReclamo'];

    // Obtener el ID de objeto relacionado con el reclamo
    $sql = "SELECT Objetos_IDObjeto FROM Reclamaciones WHERE idReclamaciones = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idReclamoCancelar);
    $stmt->execute();
    $result = $stmt->get_result();
    $objeto = $result->fetch_assoc();

    // Actualizar el estado del objeto a devuelto
    $sqlUpdate = "UPDATE Objetos SET Devuelto = 1 WHERE IDObjeto = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("i", $objeto['Objetos_IDObjeto']);
    $stmtUpdate->execute();

    // Eliminar el reclamo
    $sqlDeleteReclamo = "DELETE FROM Reclamaciones WHERE idReclamaciones = ?";
    $stmtDeleteReclamo = $conn->prepare($sqlDeleteReclamo);
    $stmtDeleteReclamo->bind_param("i", $idReclamoCancelar);
    $stmtDeleteReclamo->execute();

    header("Location: reclamos.php");
    exit();
}

if (isset($_GET['aceptarReclamo'])) {
    $idReclamo = $_GET['aceptarReclamo'];

    $sqlReclamacion = "SELECT Objetos_IDObjeto FROM Reclamaciones WHERE idReclamaciones = ?";
    $stmtReclamacion = $conn->prepare($sqlReclamacion);
    $stmtReclamacion->bind_param("i", $idReclamo);
    $stmtReclamacion->execute();
    $resultReclamacion = $stmtReclamacion->get_result();
    $objetoReclamacion = $resultReclamacion->fetch_assoc();
    $objetoReclamacionS = $objetoReclamacion['Objetos_IDObjeto'];

    $sqlObjetoUser2 = "SELECT Usuarios_IDUsuario FROM Objetos WHERE IDObjeto = ?";
    $stmtObjetoUser2 = $conn->prepare($sqlObjetoUser2);
    $stmtObjetoUser2->bind_param("i", $objetoReclamacionS);
    $stmtObjetoUser2->execute();
    $resultadoObjetoUser2 = $stmtObjetoUser2->get_result();

    $sqlUserTel = "SELECT NumTelefono FROM Usuarios WHERE IDUsuario = ?";
    $stmtUserTel = $conn->prepare($sqlUserTel);
    $stmtUserTel->bind_param("i", $idUsuarioSesion);
    $stmtUserTel->execute();
    $resultadoUserTel = $stmtUserTel->get_result();
    $resultadoUserTelF = $resultadoUserTel->fetch_assoc();
    $resultadoUserTelS = $resultadoUserTelF['NumTelefono'];

    $sqlObjR = "SELECT Recibido FROM Objetos WHERE IDObjeto = ?";
    $stmtObjR = $conn->prepare($sqlObjR);
    $stmtObjR->bind_param("i", $objetoReclamacionS);
    $stmtObjR->execute();
    $resultadoObjR = $stmtObjR->get_result();
    $resultObjRF = $resultadoObjR->fetch_assoc();
    $resultObjRS = $resultObjRF['Recibido'];

    if($resultObjRS == 0){
        $sqlUpdateR = "UPDATE Objetos SET Recibido = 1 WHERE IDObjeto = ?";
        $stmtUpdateR = $conn->prepare($sqlUpdateR);
        $stmtUpdateR->bind_param("i", $objetoReclamacionS);
        $stmtUpdateR->execute();

        $errorMensaje = "Haz aceptado el reclamo, comunícate con el reclamador: ' . $resultadoUserTelS . '";
    }else{
        $errorMensaje = "El objeto ya ha sido entregado o esta en proceso";
    }  

    header("Location: reclamos.php");
    exit();
}

if (isset($_GET['rechazarReclamo'])){
    $idReclamo = $_GET['rechazarReclamo'];

    $sqlRR = "SELECT Objetos_IDObjeto FROM Reclamaciones WHERE idReclamaciones = ?";
    $stmtRR = $conn->prepare($sqlRR);
    $stmtRR->bind_param("i", $idReclamo);
    $stmtRR->execute();
    $resultRR = $stmtRR->get_result();
    $objetoReclamo = $resultRR->fetch_assoc();
    $objetoReclamoID = $objetoReclamo['Objetos_IDObjeto'];

    $sqlUpdateRR = "UPDATE Objetos SET Recibido = 1 WHERE IDObjeto = ?";
    $stmtUpdateRR = $conn->prepare($sqlUpdateRR);
    $stmtUpdateRR->bind_param("i", $objetoReclamoID);
    $stmtUpdateRR->execute();
    echo '<script type="text/javascript">';
    echo 'alert("Reclamo rechazado.");';
    echo '</script>';   

    header("Location: reclamos.php");
    exit();
}

// Recuperar todos los reclamos para mostrar en la tabla, filtrados por el usuario que inició sesión
$sqlReclamos = "SELECT r.idReclamaciones, r.FechaEntrega, o.IDObjeto, o.Contacto, r.DetallesEntrega 
                FROM Reclamaciones r
                JOIN Objetos o ON r.Objetos_IDObjeto = o.IDObjeto
                WHERE r.Usuarios_IDUsuario = ? AND o.Recibido = 0";
$stmtReclamos = $conn->prepare($sqlReclamos);
$stmtReclamos->bind_param("i", $idUsuarioSesion);
$stmtReclamos->execute();
$resultadoReclamos = $stmtReclamos->get_result();

// Recuperar verificaciones de reclamos para mostrar en la nueva tabla
$sqlVerificaciones = "SELECT r.idReclamaciones, o.IDObjeto, r.Usuarios_IDUsuario, u.Nombre, o.Contacto 
                      FROM Reclamaciones r
                      JOIN Objetos o ON r.Objetos_IDObjeto = o.IDObjeto
                      JOIN Usuarios u ON r.Usuarios_IDUsuario = u.IDUsuario
                      WHERE o.Usuarios_IDUsuario = ? AND o.Recibido = 0";
$stmtVerificaciones = $conn->prepare($sqlVerificaciones);
$stmtVerificaciones->bind_param("i", $idUsuarioSesion);
$stmtVerificaciones->execute();
$resultadoVerificaciones = $stmtVerificaciones->get_result();


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reclamos</title>
    <link rel="stylesheet" href="Reclamos.css">
    <script>
        function mostrarMensaje(mensaje) {
            if (mensaje) {
                alert(mensaje);
            }
        }

    </script>
</head>
<body onload="mostrarMensaje('<?php echo $errorMensaje; ?>')">
    <header class="header">
        <div class="logo">
            <a href="PaginaPrincipalUser.php">
                <img src="Imagenes/logobuscatec-modified.png" alt="Logo">
            </a>
        </div>
        <button onclick="location.href='BusquedaObj.php'" class="search-btn">¿Haz perdido algo? Encuéntralo aquí</button>
        <div class="profile">
            <span>
            <?php
                if (isset($_SESSION['username'])) {
                    echo '<a href="PerfilUserEditar.php" style="text-decoration: none; color: inherit;">' . htmlspecialchars($_SESSION['username']) . '</a>';
                } else {
                    echo "Invitado";
                }
            ?>
            </span>
            <img src="Imagenes/IconoUsuario.png" alt="Usuario">
            <button class="logout-btn" onclick="location.href='Logout.php'">Cerrar sesión</button>
        </div>
    </header>

    <div class="registro-reclamo">
        <h2>Registrar Reclamo</h2>
        <form id="form-reclamo" method="POST">
            <label for="id-objeto">ID de Objeto:</label>
            <input type="text" id="id-objeto" name="id-objeto" required placeholder="Ingresa el ID del objeto a reclamar" pattern="^\d+$" title="El ID del objeto debe ser numérico" maxlength="10">
            <button type="submit">Registrar</button>
        </form>
    </div>

    <div class="tabla">
        <h2>Lista de Reclamos</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Objeto</th>
                    <th>Número de Contacto</th>
                    <th>Fecha de Reclamo</th>
                    <th>Detalles</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($reclamo = $resultadoReclamos->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $reclamo['IDObjeto']; ?></td>
                        <td><?php echo $reclamo['Contacto']; ?></td>
                        <td><?php echo $reclamo['FechaEntrega']; ?></td>
                        <td><?php echo $reclamo['DetallesEntrega']; ?></td>
                        <td>
                            <button onclick="window.location.href='reclamos.php?cancelarReclamo=<?php echo $reclamo['idReclamaciones']; ?>'">Cancelar</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="verificaciones">
        <h2>Verificaciones de Reclamos</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Objeto</th>
                    <th>Nombre del Usuario</th>
                    <th>Contacto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($verificacion = $resultadoVerificaciones->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $verificacion['IDObjeto']; ?></td>
                        <td><?php echo $verificacion['Nombre']; ?></td>
                        <td><?php echo $verificacion['Contacto']; ?></td>
                        <td>
                            <button onclick="window.location.href='reclamos.php?aceptarReclamo=<?php echo $verificacion['idReclamaciones']; ?>'">Aceptar</button>
                            <button onclick="window.location.href='reclamos.php?rechazarReclamo=<?php echo $verificacion['idReclamaciones']; ?>'">Rechazar</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>

