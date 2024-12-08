<?php
header('Content-Type: text/html; charset=UTF-8');

include('Conexion.php'); 
session_start();

// Consulta para obtener los objetos aprobados
$sql = "SELECT IDObjeto, Clasificacion, Estado, Fecha, Descripción, Usuarios_IDUsuario 
        FROM Objetos 
        WHERE Aprobado = 1";
$result = $conn->query($sql);

// Crear un array para almacenar los datos de los objetos
$objetos = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $objetos[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Objetos</title>
    <link rel="stylesheet" href="Objetos.css">
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

        <!-- Filtro para seleccionar la fecha y estado -->
        <div class="filters">
            <label for="date-filter">Fecha de registro:</label>
            <input type="date" id="date-filter" onchange="filterTable()">
            
            <label for="status-filter">Estado:</label>
            <select id="status-filter" onchange="filterTable()">
                <option value="todos">Todos</option>
                <option value="1">Encontrado</option>
                <option value="2">Perdido</option>
            </select>
        </div>

        <!-- Contador de objetos visibles -->
        <p id="contador-objetos">Total de objetos: <span id="contador">0</span></p>

        <div class="content-container">
            <h2>Lista de Objetos Aprobados</h2>

            <div class="table-container">
                <table id="objetos-table">
                    <thead>
                        <tr>
                            <th>ID Objeto</th>
                            <th>Clasificación</th>
                            <th>Estado</th>
                            <th>Descripción</th>
                            <th>ID Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($objetos as $objeto): ?>
                            <tr class="objeto-row" data-fecha="<?php echo $objeto['Fecha']; ?>" data-estado="<?php echo $objeto['Estado']; ?>">
                                <td><?php echo $objeto['IDObjeto']; ?></td>
                                <td><?php echo $objeto['Clasificacion']; ?></td>
                                <td><?php echo $objeto['Estado'] == 1 ? 'Encontrado' : 'No Encontrado'; ?></td>
                                <td><?php echo $objeto['Descripción']; ?></td>
                                <td><?php echo $objeto['Usuarios_IDUsuario']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p id="no-objects-message" class="no-objects" style="display: none;">No existen objetos registrados para los filtros seleccionados.</p>
        </div>
    </div>

    <script>
        function filterTable() {
            const dateFilter = document.getElementById('date-filter').value;
            const statusFilter = document.getElementById('status-filter').value;
            const rows = document.querySelectorAll('.objeto-row');
            let hasVisibleRows = false;
            let visibleCount = 0; // Contador de filas visibles

            rows.forEach(row => {
                const fecha = row.getAttribute('data-fecha');
                const estado = row.getAttribute('data-estado');
                
                let showRow = true;

                // Filtrar por fecha si está seleccionado
                if (dateFilter && fecha !== dateFilter) {
                    showRow = false;
                }

                // Filtrar por estado
                if (statusFilter !== "todos" && estado !== statusFilter) {
                    showRow = false;
                }

                // Mostrar u ocultar fila según los filtros
                if (showRow) {
                    row.style.display = '';
                    hasVisibleRows = true;
                    visibleCount++; // Incrementar contador si la fila es visible
                } else {
                    row.style.display = 'none';
                }
            });

            // Actualizar el contador de objetos visibles
            document.getElementById('contador').textContent = visibleCount;

            // Mostrar mensaje si no hay filas visibles
            const noObjectsMessage = document.getElementById('no-objects-message');
            noObjectsMessage.style.display = hasVisibleRows ? 'none' : 'block';
        }

        // Inicializar el contador al cargar la página
        document.addEventListener('DOMContentLoaded', filterTable);
    </script>
</body>
</html>
