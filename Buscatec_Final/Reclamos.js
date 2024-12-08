document.getElementById("form-reclamo").addEventListener("submit", function(event) {
    event.preventDefault();

    const idObjeto = document.getElementById("id-objeto").value;
    const usuario = document.getElementById("usuario").value;
    const mensajeExito = document.getElementById("mensaje-exito");

    if (isNaN(idObjeto) || idObjeto === "") {
        alert("El ID de objeto debe ser un número.");
        return;
    }
    if (!/^\d{8}$/.test(usuario)) {
        alert("El usuario debe ser un número de 8 dígitos.");
        return;
    }
    mensajeExito.style.display = "block";
    document.getElementById("form-reclamo").reset();
    setTimeout(() => {
        mensajeExito.style.display = "none";
    }, 3000);
});