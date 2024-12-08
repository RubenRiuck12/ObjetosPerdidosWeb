<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda</title>
    <link rel="stylesheet" href="BusquedaObj.css">
    <script>
        // Limpiar los filtros solo cuando la página se recarga (cuando no hay parámetros en la URL)
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (!urlParams.has('classification') && !urlParams.has('start_date') && !urlParams.has('end_date')) {
                // Si no hay parámetros, restablecer los valores de los filtros
                document.querySelector('select[name="classification"]').value = "";
                document.querySelector('input[name="start_date"]').value = "";
                document.querySelector('input[name="end_date"]').value = "";
            }
        };
    </script>
</head>
<body>
    <header class="header">
        <div class="logo">
            <a href="PaginaPrincipalUser.php">
                <img src="Imagenes/logobuscatec-modified.png" alt="Logo">
            </a>
        </div>
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

    <div class="container">
        <section class="search-section">
            <h2>Búsqueda:</h2>
            <form method="GET" action="BusquedaObj.php">
                <div class="filters">
                    <select name="classification" class="input-field">
                        <option value="">Selecciona una categoría</option>
                        <option value="1" <?php echo (isset($_GET['classification']) && $_GET['classification'] == '1') ? 'selected' : ''; ?>>Ropa</option>
                        <option value="2" <?php echo (isset($_GET['classification']) && $_GET['classification'] == '2') ? 'selected' : ''; ?>>Dinero</option>
                        <option value="3" <?php echo (isset($_GET['classification']) && $_GET['classification'] == '3') ? 'selected' : ''; ?>>Útiles escolares</option>
                        <option value="4" <?php echo (isset($_GET['classification']) && $_GET['classification'] == '4') ? 'selected' : ''; ?>>Tecnología</option>
                        <option value="5" <?php echo (isset($_GET['classification']) && $_GET['classification'] == '5') ? 'selected' : ''; ?>>Joyería</option>
                        <option value="6" <?php echo (isset($_GET['classification']) && $_GET['classification'] == '6') ? 'selected' : ''; ?>>K-pop</option>
                        <option value="7" <?php echo (isset($_GET['classification']) && $_GET['classification'] == '7') ? 'selected' : ''; ?>>Otro</option>
                    </select>
                    <input type="date" name="start_date" value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>">
                    <input type="date" name="end_date" value="<?php echo isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : ''; ?>">
                    <button type="submit">Buscar</button>
                </div>
            </form>
        </section>

        <section class="results-section">
            <?php
            include 'Conexion.php';

            // Parámetros para la paginación
            $records_per_page = 4;
            $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $start_from = ($current_page - 1) * $records_per_page;

            // Obtener parámetros de clasificación y fecha
            $classification = isset($_GET['classification']) ? $_GET['classification'] : '';
            $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
            $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

            // Construir la consulta SQL con filtros si se aplican
            $sql = "SELECT o.IDObjeto, o.Clasificacion, o.Estado, o.Fecha, o.Descripción, u.Nombre AS Usuario, o.Contacto, o.Foto 
                    FROM Objetos o 
                    JOIN Usuarios u ON o.Usuarios_IDUsuario = u.IDUsuario
                    WHERE o.Aprobado = 1";  // Aseguramos que solo se muestren objetos aprobados

            // Añadir condiciones solo si los filtros están presentes
            $conditions = [];
            if ($classification) {
                $conditions[] = "o.Clasificacion = $classification";
            }
            if ($start_date) {
                $conditions[] = "o.Fecha >= '$start_date'";
            }
            if ($end_date) {
                $conditions[] = "o.Fecha <= '$end_date'";
            }

            // Si hay filtros, aplicarlos a la consulta
            if (!empty($conditions)) {
                $sql .= " AND " . implode(' AND ', $conditions);
            }

            $sql .= " ORDER BY o.Fecha DESC LIMIT $start_from, $records_per_page";

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
                    <div class="object-item">
                        <div class="object-image">
                            <img src="' . $foto . '" alt="Objeto">
                            <span class="estado ' . strtolower($estado) . '">Estado: ' . $estado . '</span>
                        </div>
                        <div class="object-info">
                            <p>ID: ' . $row['IDObjeto'] . '</p>
                            <p>Clasificación: ' . $clasificacion_text . '</p>
                            <p>Fecha: ' . $row['Fecha'] . '</p>
                            <p>Descripción: ' . $row['Descripción'] . '</p>
                            <p>Encontrado por: ' . $row['Usuario'] . '</p>
                            <p>Contacto: ' . $row['Contacto'] . '</p>
                        </div>
                    </div>';
                }
            } else {
                echo '<p>No hay objetos registrados para los filtros seleccionados.</p>';
            }

            // Consulta para contar registros
            $sql_count = "SELECT COUNT(IDObjeto) AS total FROM Objetos WHERE Aprobado = 1"; // Solo contar los aprobados
            if (!empty($conditions)) {
                $sql_count .= " AND " . implode(' AND ', $conditions);
            }
            $count_result = $conn->query($sql_count);
            $total_records = $count_result->fetch_assoc()['total'];
            $total_pages = ceil($total_records / $records_per_page);

            // Paginación
            echo '<div class="pagination">';
            for ($i = 1; $i <= $total_pages; $i++) {
                $page_url = "BusquedaObj.php?page=$i";
                if ($classification) $page_url .= "&classification=" . urlencode($classification);
                if ($start_date) $page_url .= "&start_date=" . urlencode($start_date);
                if ($end_date) $page_url .= "&end_date=" . urlencode($end_date);
                if ($i == $current_page) {
                    echo '<span class="current-page">' . $i . '</span>';
                } else {
                    echo '<a href="' . $page_url . '">' . $i . '</a>';
                }
            }
            echo '</div>';
            ?>
        </section>
    </div>
</body>
</html>


