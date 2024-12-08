<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Objeto</title>
    <link rel="stylesheet" href="RegistroObjeto.css">
</head>
<body>
    <div class="registro">
        <header class="header">
            <div class="logo">
                <a href="PaginaPrincipalUser.php">
                    <img src="Imagenes/logobuscatec-modified.png" alt="Logo">
                </a>
            </div>
            <button onclick="location.href='BusquedaObj.php'" class="search-btn">¿Haz perdido algo? Encuéntralo aquí</button>
            <div class="profile">
                <a href="PerfilUserEditar.php" id="nombre-usuario">
                <?php
                    if (isset($_SESSION['username'])) {
                        echo htmlspecialchars($_SESSION['username']);
                    } else {
                        echo "Invitado";
                    }
                    ?>
                </a>
                <img src="Imagenes/IconoUsuario.png" alt="Usuario">
                <button class="logout-btn" onclick="location.href='Logout.php'">Cerrar sesión</button>
            </div>
        </header>

        <div class="container">
            <h2>Registro de objeto</h2>
            <form class="registro-form" enctype="multipart/form-data" action="RegistroObjetoB.php" method="POST">
                
                <div class="image-column">
                    <div class="image-upload">
                        <img src="Imagenes/IconoFoto.png" alt="Imagen del objeto" id="preview-img">
                        <input type="file" name="foto" id="foto" style="display: none;" accept="image/*">
                        <button type="button" class="upload-btn" onclick="document.getElementById('foto').click()">Cargar imagen</button>
                    </div>
                </div>

                <div class="info-column">
                    <div class="form-group">
                        <label for="estado">El objeto fue:</label>
                        <select id="estado" name="estado" class="input-field">
                            <option value="1">Perdido</option>
                            <option value="2">Encontrado</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fecha">Fecha:</label>
                        <input type="date" id="fecha" name="fecha" class="input-field" required>
                    </div>
                    <div class="form-group">
                        <label for="contacto">Contacto:</label>
                        <input type="text" id="contacto" name="contacto" class="input-field" placeholder="Medio para ponerse en contacto." required maxlength="10">
                    </div>
                </div>

                <div class="info-column">
                    <div class="form-group">
                        <label for="clasificacion">Clasificación:</label>
                        <select id="clasificacion" name="clasificacion" class="input-field">
                            <option value="1">Ropa</option>
                            <option value="2">Dinero</option>
                            <option value="3">Útiles escolares</option>
                            <option value="4">Tecnología (PCs, relojes inteligentes, celulares...)</option>
                            <option value="5">Joyería</option>
                            <option value="6">K-pop</option>
                            <option value="7">Otro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nombre-propietario">Usuario:</label>
                        <input type="text" id="nombre-propietario" name="nombre-propietario" 
                               class="input-field" 
                               value="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>" 
                               readonly>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción:</label>
                        <textarea id="descripcion" name="descripcion" class="input-field descripcion" placeholder="Agrega una breve descripción del objeto en general." required maxlength="279"></textarea>
                    </div>
                    <button type="submit" class="save-btn">Guardar objeto</button>
                </div>
            </form>
        </div>
    </div>
<script>
    // Validación y previsualización de la imagen
    document.getElementById('foto').addEventListener('change', function (event) {
        const file = event.target.files[0];
        const preview = document.getElementById('preview-img');

        if (file) {
            // Validar tipo de archivo
            if (!file.type.startsWith('image/')) {
                alert('Por favor, selecciona un archivo de imagen.');
                this.value = ''; // Resetear el input
                return;
            }

            // Mostrar vista previa de la imagen
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result; // Actualizar el src con la imagen cargada
            };
            reader.readAsDataURL(file);
        }
    });

    // Validación del formulario
    function validateForm(event) {
        let valid = true; // Variable para determinar si el formulario es válido
        const contacto = document.getElementById('contacto').value;
        const descripcion = document.getElementById('descripcion').value;
        const fecha = document.getElementById('fecha').value;
        const today = new Date().toISOString().split('T')[0];
        let errorMessages = []; // Lista de mensajes de error

        // Validar contacto (10 dígitos)
        if (!/^\d{10}$/.test(contacto)) {
            errorMessages.push('El campo de contacto debe ser numérico y contener exactamente 10 dígitos.');
            valid = false;
        }

        // Validar descripción
        if (descripcion.length > 279) {
            errorMessages.push('La descripción no debe superar los 279 caracteres.');
            valid = false;
        }

        // Validar fecha (no puede ser futura)
        if (fecha > today) {
            errorMessages.push('La fecha no puede exceder del día actual.');
            valid = false;
        }

        // Mostrar mensajes de error si existen
        if (!valid) {
            alert(errorMessages.join('\n'));
            if (event) event.preventDefault(); // Evitar el envío del formulario
            return false;
        }

        // Si las validaciones son correctas, mostrar ventana emergente de confirmación
        const confirmacion = confirm(
            'Verifica que todos los datos sean correctos porque una vez que registres el objeto este será enviado para su verificación y ya no podrás editarlo. Ten encuenta que mientras tu objeto sea revisado no aparecerá en el motor de búsqueda, Novedades o permitirá reclamarlo pero podrás visualizarlo en Mis objetos en tu perfil, al cual puedes acceder haciendo click en tu nombre, una vez comprendido todo esto ¿Deseas continuar?'
        );

        if (!confirmacion) {
            event.preventDefault(); // Detener el envío del formulario si el usuario cancela
            return false;
        }

        // Si acepta la confirmación, continuar con el envío del formulario
        alert('Registro exitoso de objeto.');
        return true;
    }

    // Asignar validación al evento submit del formulario
    document.querySelector('.registro-form').addEventListener('submit', function (event) {
        // Llamar a validateForm y evitar el envío si no es válido
        if (!validateForm(event)) {
            event.preventDefault(); // Detener el envío del formulario si la validación falla
        }
    });
</script>

</body>
</html>


