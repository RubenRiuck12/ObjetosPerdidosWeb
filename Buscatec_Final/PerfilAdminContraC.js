function validateAndSavePassword() {
    const oldPassword = document.getElementById("contraseñaold").value;
    const newPassword = document.getElementById("contraseñanew").value;
    const newPasswordRe = document.getElementById("contraseñanewre").value;

    // Expresión regular para validar la contraseña
    const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[-*?!@#$.])[a-zA-Z0-9\-*?!@#$.]{8}$/;

    // Validar que las contraseñas nuevas coincidan
    if (newPassword !== newPasswordRe) {
        alert("Las contraseñas nuevas no coinciden.");
        return;
    }

    // Validar que las contraseñas nuevas respeten la expresión regular
    if (!passwordRegex.test(newPassword)) {
        alert("La contraseña nueva no cumple con los requisitos de seguridad.");
        return;
    }

    // Enviar el formulario si las validaciones son correctas
    const form = document.getElementById("profileForm");
    form.submit();
}