<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="PaginaPrincipalUser.css">
</head>
<body>
    <div class="inicio">
        <header class="header">
        <div class="logo">
                <a href="PaginaPrincipalUser.php">
                    <img src="Imagenes/logobuscatec-modified.png" alt="Logo">
                </a>
            </div>
            <!-- Botón que lleva a BusquedaObj.html -->
            <button onclick="location.href='BusquedaObj.php'" class="search-btn">¿Haz perdido algo? Encuéntralo aquí</button>
            <div class="profile">
                <span>
                <?php
                    // Verifica si el usuario ha iniciado sesión y muestra el nombre
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

        <div class="container">
            <section class="novedades">
                <h2>Novedades</h2>
                
                <?php
                include 'Conexion.php'; // Asegúrate de tener el archivo de conexión a la base de datos

                // Parámetros para la paginación
                $records_per_page = 3;
                $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $start_from = ($current_page - 1) * $records_per_page;

                // Consulta para obtener los objetos de la base de datos con límite para paginación
                $sql = "SELECT IDObjeto, Clasificacion, Estado, Fecha, Descripción, Nombre AS Usuario, Contacto, Foto, Aprobado 
                        FROM Objetos o 
                        JOIN Usuarios u ON Usuarios_IDUsuario = IDUsuario
                        ORDER BY Fecha DESC
                        LIMIT $start_from, $records_per_page";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        if($row['Aprobado'] == 1){
                            // Convertir el estado y la clasificación en texto
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

                            // Mostrar la imagen si está en la base de datos o una por defecto
                            $foto = ($row['Foto']) ? 'data:image/jpeg;base64,' . base64_encode($row['Foto']) : 'Imagenes/IconoFoto.png';

                            echo '
                            <div class="item">
                                <div class="item-image">
                                    <img src="' . $foto . '" alt="Imagen del objeto">
                                    <span class="estado ' . strtolower($estado) . '">Estado: ' . $estado . '</span>
                                </div>
                                <div class="item-info">
                                    <p>ID: ' . $row['IDObjeto'] . '</p>
                                    <p>Clasificacion: ' . $clasificacion_text . '</p>
                                    <p>Fecha: ' . $row['Fecha'] . '</p>
                                    <p>Descripcion: ' . $row['Descripcion'] . '</p>
                                    <p>Encontrado por: ' . $row['Usuario'] . '</p>
                                    <p>Contacto: ' . $row['Contacto'] . '</p>
                                </div>
                            </div>';
                        }
                    }
                } else {
                    echo '<p>No hay objetos registrados.</p>';
                }

                // Consulta para contar el total de registros
                $sql_count = "SELECT COUNT(IDObjeto) AS total FROM Objetos";
                $count_result = $conn->query($sql_count);
                $total_records = $count_result->fetch_assoc()['total'];
                $total_pages = ceil($total_records / $records_per_page);

                // Enlaces de paginación
                echo '<div class="pagination">';
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $current_page) {
                        echo '<span class="current-page">' . $i . '</span>';
                    } else {
                        echo '<a href="PaginaPrincipalUser.php?page=' . $i . '">' . $i . '</a>';
                    }
                }
                echo '</div>';

                $conn->close();
                ?>
                
            </section>
            <aside class="add-object">
                <!-- Botón que lleva a RegistroObjeto.php -->
                <button class="add-btn" onclick="location.href='RegistroObjeto.php'">+</button>
                <span>Agregar objeto perdido/encontrado</span>
                <button class="add-btn" onclick="location.href='Reclamos.php'">+</button>
                <span>Reclamos de objetos</span>
            </aside>
        </div>
    </div>
    <script src="PaginaPrincipalUser.js"></script>
</body>
</html>