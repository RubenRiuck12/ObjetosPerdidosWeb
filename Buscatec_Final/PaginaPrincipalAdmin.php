<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador</title>
    <link rel="stylesheet" href="PaginaPrincipalAdmin.css">
</head>
<body>
    <div class="inicio">
        <header class="header">
            <div class="logo">
                <a href="PaginaPrincipalAdmin.php">
                    <img src="Imagenes/logobuscatec-modified.png" alt="Logo">
                </a>
            </div>
            <div class="profile">
    <div>
    <?php
    session_start();
        // Verifica si el usuario ha iniciado sesión y muestra el nombre
        if (isset($_SESSION['username'])) {
            echo '<a href="PerfilAdministradorEditar.php" style="text-decoration: none; color: inherit;">' . htmlspecialchars($_SESSION['username']) . '</a>';
        } else {
            echo "Invitado";
        }
        ?>
        <br> 
        <a href="PerfilAdministradorEditar.php" style="text-decoration: none; color: inherit; font-size: 0.75em;">Administrador</a>
    </div>
    <img src="Imagenes/IconoUsuario.png" alt="Usuario" class="perfilicon">
    <button class="logout-btn" onclick="location.href='Logout.php'">Cerrar sesión</button>
</div>
        </header>

        <div class="container">
            <section class="novedades">
                <h2>Revisiones</h2>

                <?php
                include 'Conexion.php';
                
                // Consulta para obtener los objetos pendientes de aprobación (Aprobado = 2)
                $sql = "SELECT o.IDObjeto, o.Clasificacion, o.Estado, o.Fecha, o.Descripcion, u.Nombre AS Usuario, o.Contacto, o.Foto
                        FROM objetos o
                        JOIN usuarios u ON o.Usuarios_IDUsuario = u.IDUsuario
                        WHERE o.Aprobado = 2
                        ORDER BY o.Fecha DESC";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $estado = ($row['Estado'] == 1) ? "Perdido" : "Encontrado";
                        
                        switch ($row['Clasificacion']) {
                            case 1: $clasificacion_text = "Ropa"; break;
                            case 2: $clasificacion_text = "Dinero"; break;
                            case 3: $clasificacion_text = "Útiles escolares"; break;
                            case 4: $clasificacion_text = "Tecnología"; break;
                            case 5: $clasificacion_text = "Joyería"; break;
                            case 6: $clasificacion_text = "K-pop"; break;
                            default: $clasificacion_text = "Otro";
                        }

                        $foto = ($row['Foto']) ? 'data:image/jpeg;base64,' . base64_encode($row['Foto']) : 'Imagenes/IconoFoto.png';

                        echo '
                        <div class="item">
                            <div class="item-image">
                                <img src="' . $foto . '" alt="Imagen del objeto">
                            </div>
                            <div class="item-info">
                                <p>ID: ' . $row['IDObjeto'] . '</p>
                                <p>Clasificación: ' . $clasificacion_text . '</p>
                                <p>Fecha: ' . $row['Fecha'] . '</p>
                                <p>Descripción: ' . $row['Descripcion'] . '</p>
                                <p>Encontrado por: ' . $row['Usuario'] . '</p>
                                <p>Contacto: ' . $row['Contacto'] . '</p>
                                <form method="POST" action="ProcesarObj.php">
                                    <input type="hidden" name="IDObjeto" value="' . $row['IDObjeto'] . '">
                                    <button type="submit" name="accion" value="aprobar" class="accept-btn">Aceptar</button>
                                    <button type="submit" name="accion" value="rechazar" class="reject-btn">Rechazar</button>
                                </form>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<p>No hay objetos pendientes de revisión.</p>';
                }

                $conn->close();
                ?>
            </section>
        </div>
    </div>
</body>
</html>