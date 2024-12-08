document.getElementById("editButton").addEventListener("click", () => {
    const inputFields = document.querySelectorAll(".input-field");
    inputFields.forEach(input => input.disabled = false);
    document.getElementById("editButton").style.display = "none";
    document.getElementById("saveButton").style.display = "inline";
    document.getElementById("changeButton").style.display = "inline";
});

document.getElementById("saveButton").addEventListener("click", () => {
    const telefonoRegex = /^\d{10}$/;
    const curpRegex = /^[A-Z0-9]{18}$/;
    const emailRegex = /^[a-zA-Z0-9._%+-]+@(gmail\.com|hotmail\.com|veracruz\.tecnm\.mx)$/;

    // Obtener los valores de los campos
    const telefono = document.getElementById("telefono").value;
    const curp = document.getElementById("curp").value;
    const email = document.getElementById("email").value;

    // Validación de teléfono
    if (!telefonoRegex.test(telefono)) {
        alert("El número de teléfono debe ser de 10 dígitos.");
        event.preventDefault();  // Detener el envío del formulario
        return;
    }

    // Validación de CURP
    if (!curpRegex.test(curp)) {
        alert("La CURP debe tener 18 caracteres alfanuméricos.");
        event.preventDefault();  // Detener el envío del formulario
        return;
    }

    // Validación de email
    if (!emailRegex.test(email)) {
        alert("El email debe ser de los dominios permitidos (gmail.com, hotmail.com, veracruz.tecnm.mx).");
        event.preventDefault();  // Detener el envío del formulario
        return;
    }

    document.getElementById("profileForm").submit();
});