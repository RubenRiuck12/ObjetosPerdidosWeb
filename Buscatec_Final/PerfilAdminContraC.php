
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="PerfilAdministrador.css">
</head>
<body>
<div id="app" class="perfil">
    <header class="header">
            <div class="logo">
            <a href="PaginaPrincipalAdmin.php">
                <img src="Imagenes/logobuscatec-modified.png" alt="Logo">
            </a>
            </div>
            <img src="Imagenes/3.png" alt="Usuario" class="logoescuela">
            <div class="profile">
            <button class="logout-btn" onclick="location.href='Logout.php'">Cerrar sesión</button>
            </div>
        </header>

        <div class="container">
            <section class="profile-section">
                <div class="left-section">
                    <h2>Perfil</h2>
                    <div class="profile-image">
                        <img src="Imagenes/IconoUsuario.png" alt="Foto de perfil">
                        <input type="file" accept="image/*" id="fileInput" style="display: none;" onchange="handleFileUpload(event)">
                        <button id="saveButton" class="upload-btn" onclick="validateAndSavePassword()">Guardar cambios</button>
                    </div>
                </div>
                <div class="right-section">
                    <form class="profile-form" id="profileForm" method="POST" action="PerfilUserContraC.php">
                        <div class="form-group">
                            <label for="contraseñaold">Ingrese contraseña anterior:</label>
                            <input type="password" id="contraseñaold" name="contraseñaold" class="input-field">
                        </div>
                        <div class="form-group">
                            <label for="contraseñanew">Ingresar contraseña nueva:</label>
                            <input type="password" id="contraseñanew" name="contraseñanew" class="input-field">
                        </div>
                        <div class="form-group">
                            <label for="contraseñanewre">Repetir contraseña nueva:</label>
                            <input type="password" id="contraseñanewre" name="contraseñanewre" class="input-field">
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
    <script src="PerfilAdminContraC.js"></script>
</body>
</html>