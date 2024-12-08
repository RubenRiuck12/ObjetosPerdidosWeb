document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registration-form');
    const passwordInput = document.getElementById('password');
    const passwordError = document.getElementById('password-error');
    const modal = document.getElementById('modal');
    const closeModal = document.getElementById('close-btn');
    const successModal = document.getElementById('success-modal');
    const closeSuccessBtn = document.getElementById('close-success-btn');

    // Expresión regular para la contraseña
    const passwordPattern = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[-*?!@#$.])[a-zA-Z0-9\-*?!@#$.]{8}$/;

    // Validación en tiempo real
    passwordInput.addEventListener('input', function () {
        if (passwordPattern.test(passwordInput.value)) {
            passwordError.textContent = ''; // Limpia errores
            passwordError.style.display = 'none';
            passwordInput.classList.remove('error');
            passwordInput.classList.add('valid');
        } else {
            passwordError.textContent = 'La contraseña debe tener exactamente 8 caracteres, incluyendo una mayúscula, una minúscula, un dígito y un carácter especial (-*?!@#$.).';
            passwordError.style.display = 'block';
            passwordInput.classList.add('error');
            passwordInput.classList.remove('valid');
        }
    });

    // Cierra el modal inicial
    closeModal.addEventListener('click', function () {
        modal.style.display = 'none';
    });

    // Validación final al enviar
    form.addEventListener('submit', function (e) {
        if (!passwordPattern.test(passwordInput.value)) {
            e.preventDefault(); // Detén el envío del formulario
            passwordError.textContent = 'La contraseña no cumple con los requisitos.';
            passwordError.style.display = 'block';
            passwordInput.classList.add('error');
        } else {
            // Simula un éxito: puedes manejar esto según tus necesidades
            e.preventDefault(); // Solo para pruebas, remueve esto si quieres enviar al servidor
            successModal.style.display = 'flex';
            console.log('Formulario enviado correctamente');
        }
    });

    // Cierra la ventana de éxito
    closeSuccessBtn?.addEventListener('click', function () {
        successModal.style.display = 'none';
        window.location.href = '/'; // Redirige a la página principal o donde desees
    });
});
