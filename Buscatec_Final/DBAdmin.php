<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio DBA</title>
    <link rel="stylesheet" href="DBAdmin.css">
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
        
        <div class="main-content">
            <div class="column">
                <label for="usuarios">Usuarios</label>
                <button onclick="location.href='Usuarios.php'">Ver usuarios</button>
                <button onclick="location.href='EditarUsuario.php'">Editar usuario</button>
                <button onclick="location.href='EliminarUsuario.php'">Eliminar usuario</button>
                <button onclick="location.href='AgregarUsuario.php'">Agregar usuario</button>
            </div>
            <div class="column">
                <label for="objetos">Objetos</label>
                <button onclick="location.href='Objetos.php'">Ver objetos</button>
                <button onclick="location.href='EliminarObjeto.php'">Eliminar objeto</button>
                <button onclick="location.href='ObjetosDevueltos.php'">Devoluciones</button>
            </div>
        </div>
    </div>
</body>
</html>


