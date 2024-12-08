<?php
header('Content-Type: text/html; charset=UTF-8');

include('Conexion.php'); 
session_start();

// Consulta para obtener los usuarios con los campos requeridos
$sql = "SELECT IDUsuario, Nombre, CURP, NumTelefono, Email, Rol FROM Usuarios";
$result = $conn->query($sql);

// Crear un array para almacenar los datos de los usuarios
$usuarios = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Convertir el valor numérico del rol a su representación en texto
        switch ($row['Rol']) {
            case 1:
                $row['Rol'] = "General";
                break;
            case 2:
                $row['Rol'] = "Administrador";
                break;
            case 3:
                $row['Rol'] = "DBA";
                break;
            default:
                $row['Rol'] = "Desconocido";
                break;
        }
        $usuarios[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
    <link rel="stylesheet" href="Usuarios.css">
    <style>
        /* Estilo para el contenedor desplazable */
        .table-container {
            max-width: 100%;
            height: 200px; /* Altura fija para el contenedor de la tabla */
            overflow-y: auto; /* Desplazamiento vertical */
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .no-users {
            color: red;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }
        .user-count {
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            text-align: right;
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

            <!-- Contenedor con desplazamiento -->
            <h2>Lista de Usuarios</h2>

            <!-- Filtro para seleccionar el tipo de usuario -->
            <label for="role-filter">Filtrar por Rol:</label>
            <select id="role-filter" onchange="filterTable()">
                <option value="todos">Todos</option>
                <option value="General">General</option>
                <option value="Administrador">Administrador</option>
                <option value="DBA">DBA</option>
            </select>

            <!-- Contenedor para mostrar la cantidad de usuarios filtrados -->
            <div id="user-count" class="user-count">Total de usuarios: <?php echo count($usuarios); ?></div>

            <div class="table-container">
                <table id="usuarios-table">
                    <thead>
                        <tr>
                            <th>ID Usuario</th>
                            <th>Nombre</th>
                            <th>CURP</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Rol</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr class="usuario-row" data-role="<?php echo $usuario['Rol']; ?>">
                                <td><?php echo $usuario['IDUsuario']; ?></td>
                                <td><?php echo $usuario['Nombre']; ?></td>
                                <td><?php echo $usuario['CURP']; ?></td>
                                <td><?php echo $usuario['NumTelefono']; ?></td>
                                <td><?php echo $usuario['Email']; ?></td>
                                <td><?php echo $usuario['Rol']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p id="no-users-message" class="no-users" style="display: none;">No existen usuarios registrados para este rol.</p>
        </div>
    </div>

    <script>
        function filterTable() {
            const filter = document.getElementById('role-filter').value;
            const rows = document.querySelectorAll('.usuario-row');
            let hasVisibleRows = false;
            let visibleCount = 0;

            rows.forEach(row => {
                const role = row.getAttribute('data-role');
                
                // Mostrar todas las filas si el filtro es "todos"
                if (filter === "todos" || role === filter) {
                    row.style.display = '';
                    hasVisibleRows = true;
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Mostrar el mensaje si no hay usuarios para el filtro seleccionado
            const noUsersMessage = document.getElementById('no-users-message');
            if (!hasVisibleRows) {
                noUsersMessage.style.display = 'block';
            } else {
                noUsersMessage.style.display = 'none';
            }

            // Actualizar el contador de usuarios filtrados
            const userCountDiv = document.getElementById('user-count');
            userCountDiv.textContent = `Total de usuarios: ${visibleCount}`;
        }

        // Llamada a la función para mostrar el contador al cargar la página
        window.onload = function() {
            filterTable();
        }
    </script>
</body>
</html>
