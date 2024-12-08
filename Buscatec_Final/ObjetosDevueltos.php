<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
include('Conexion.php'); // Asegúrate de que este archivo define correctamente $conexion
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devoluciones</title>
    <link rel="stylesheet" href="ObjetosDevueltos.css">
    <style>
        /* Contenedor para habilitar desplazamiento horizontal */
        .table-container {
            width: 80%; /* Ancho del contenedor (puedes ajustar este valor) */
            margin: 20px auto; /* Centrado del contenedor */
            overflow-x: auto; /* Permitir desplazamiento horizontal */
        }

        table {
            width: 1200px; /* Establece un ancho fijo para la tabla */
            border-collapse: collapse;
            font-size: 0.9em; /* Reduce el tamaño de la letra */
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        h1 {
            text-align: center;
            padding-top: 12px;
        }

        th {
            background-color: #4CAF50; /* Color de encabezado */
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        .no-entries {
            text-align: center;
            font-style: italic;
        }

        .count-container {
            text-align: center;
            margin: 20px 0;
            font-size: 1.1em;
        }
    </style>
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

        <h1>Objetos devueltos</h1>

        <?php
        // Variable para almacenar el conteo de objetos devueltos
        $countQuery = "
        SELECT COUNT(*) AS total
        FROM 
            Objetos o
        JOIN 
            Reclamaciones r 
            ON o.IDObjeto = r.Objetos_IDObjeto AND o.Usuarios_IDUsuario = r.Objetos_Usuarios_IDUsuario
        JOIN 
            Usuarios u1 
            ON o.Usuarios_IDUsuario = u1.IDUsuario
        JOIN 
            Usuarios u2 
            ON r.Usuarios_IDUsuario = u2.IDUsuario
        WHERE 
            o.Estado = 1;";

        $countResult = $conexion->query($countQuery);
        $countRow = $countResult->fetch_assoc();
        $totalObjetos = $countRow['total'];
        ?>

        <div class="count-container">
            <strong>Total de objetos devueltos: <?php echo $totalObjetos; ?></strong>
        </div>

        <!-- Contenedor con desplazamiento horizontal -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID Objeto</th>
                        <th>Descripción</th>
                        <th>ID Publicador</th>
                        <th>Nombre Publicador</th>
                        <th>ID Reclamante</th>
                        <th>Nombre Reclamante</th>
                        <th>Fecha de Reclamación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($conexion) {
                        $query = "
                        SELECT 
                            o.IDObjeto,
                            o.Descripción,
                            u1.IDUsuario AS IDPublicador,
                            u1.Nombre AS NombrePublicador,
                            r.Usuarios_IDUsuario AS IDReclamante,
                            u2.Nombre AS NombreReclamante,
                            r.FechaEntrega AS FechaReclamacion
                        FROM 
                            Objetos o
                        JOIN 
                            Reclamaciones r 
                            ON o.IDObjeto = r.Objetos_IDObjeto AND o.Usuarios_IDUsuario = r.Objetos_Usuarios_IDUsuario
                        JOIN 
                            Usuarios u1 
                            ON o.Usuarios_IDUsuario = u1.IDUsuario
                        JOIN 
                            Usuarios u2 
                            ON r.Usuarios_IDUsuario = u2.IDUsuario
                        WHERE 
                            o.Estado = 1;";

                        $result = $conexion->query($query);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['IDObjeto']}</td>
                                        <td>{$row['Descripción']}</td>
                                        <td>{$row['IDPublicador']}</td>
                                        <td>{$row['NombrePublicador']}</td>
                                        <td>{$row['IDReclamante']}</td>
                                        <td>{$row['NombreReclamante']}</td>
                                        <td>{$row['FechaReclamacion']}</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='no-entries'>No se encontraron reclamaciones</td></tr>";
                        }

                        $conexion->close();
                    } else {
                        echo "<tr><td colspan='7'>Error de conexión a la base de datos</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

