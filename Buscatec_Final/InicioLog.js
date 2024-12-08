document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('login-form');
    const usuarioInput = document.getElementById('usuario');
    const contraseñaInput = document.getElementById('contraseña');
    const registerLink = document.getElementById('register-link'); // Suponiendo que tienes un enlace para registro
    const modal = document.getElementById('register-modal'); // Suponiendo que tienes un modal para registro
    const closeModal = document.getElementById('close-modal'); // Suponiendo que tienes un botón para cerrar el modal
    const errorMessage = document.getElementById('error-message');
    

    // Expresiones regulares para validación
    const usuarioPattern = /^.{8}$/; // Longitud exacta de 8 caracteres
    const contraseñaPattern = /^.{8}$/; // Validaciones de contraseña
    
    // Validación dinámica de usuario
    usuarioInput.addEventListener('input', function () {
        if (!usuarioPattern.test(usuarioInput.value)) {
            usuarioInput.setCustomValidity('El nombre de usuario debe contener exactamente 8 caracteres.');
        } else {
            usuarioInput.setCustomValidity('');
        }
        form.reportValidity();
    });
    
    // Validación dinámica de contraseña
    contraseñaInput.addEventListener('input', function () {
        if (!contraseñaPattern.test(contraseñaInput.value)) {
            contraseñaInput.setCustomValidity(
                'La contraseña debe tener exactamente 8 caracteres.\n' 
            );
        } else {
            contraseñaInput.setCustomValidity('');
        }
        form.reportValidity();
    });    

    

    // Validación del formulario al enviarlo
    form.addEventListener('submit', function (e) {
        e.preventDefault(); // Evitar el envío del formulario para realizar la validación

        let isValid = true;

        const formData = new FormData();
        formData.append('usuario', usuarioInput.value);
        formData.append('contraseña', contraseñaInput.value);

        if (!usuarioPattern.test(usuarioInput.value)) {
            usuarioInput.setCustomValidity('El nombre de usuario debe contener exactamente 5 caracteres.');
            isValid = false;
        }

        if (!contraseñaPattern.test(contraseñaInput.value)) {
            contraseñaInput.setCustomValidity(
                'La contraseña debe tener exactamente 8 caracteres.\n' +
                'Debe incluir al menos un número.\n' +
                'Debe ser una combinación de mayúsculas y minúsculas, y no debe contener espacios.\n' +
                'Solo se permiten los caracteres especiales: - * ? ! @ # $ .'
            );
            isValid = false;
        }

        // Si todo es válido, se puede enviar el formulario
        /*if (isValid) {
            alert('Formulario enviado correctamente');
            window.location.href = 'PaginaPrincipalUser.html'; // Redirección a otra página
        } else {
            form.reportValidity();
        }*/
            fetch('InicioLog.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                if (result.startsWith('success')) {
                    const role = result.split('-')[1]; // Extrae el rol de la respuesta
            
                    // Redirige según el rol
                    switch (role) {
                        case '1':
                            window.location.href = 'PaginaPrincipalUser.php'; // Página para usuario general
                            break;
                        case '2':
                            window.location.href = 'PaginaPrincipalAdmin.php'; // Página para administrador
                            break;
                        case '3':
                            window.location.href = 'DBAdmin.php'; // Página para DBA
                            break;
                        default:
                            errorMessage.textContent = 'Rol desconocido.';
                            errorMessage.style.display = 'block';
                    }
                } else {
                    errorMessage.textContent = 'Usuario o contraseña incorrectos.';
                    errorMessage.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorMessage.textContent = 'Hubo un error en el inicio de sesión.';
                errorMessage.style.display = 'block';
            });
    });

    // Mostrar el modal de registro
    registerLink.addEventListener('click', function (e) {
        e.preventDefault();
        modal.style.display = 'flex';
    });

    // Cerrar el modal de registro
    closeModal.addEventListener('click', function () {
        modal.style.display = 'none';
    });

    // Cerrar el modal si el usuario hace clic fuera de él
    window.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });

    
});

