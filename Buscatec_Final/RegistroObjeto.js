// Función para abrir el selector de archivos y permitir solo imágenes
function uploadImage() {
    const input = document.createElement("input");
    input.type = "file";
    input.accept = "image/jpeg, image/png"; // Aceptar solo archivos JPEG y PNG

    input.onchange = () => {
        const file = input.files[0];
        if (file) {
            // Validación de tipo de archivo
            const validExtensions = ["image/jpeg", "image/png"];
            if (!validExtensions.includes(file.type)) {
                alert("Por favor, selecciona un archivo de imagen (JPEG o PNG).");
                return;
            }
            console.log("Cargar imagen:", file.name);
        }
    };

    // Activa el selector de archivos
    input.click();
}

// Función para guardar la información del objeto
function saveObject() {
    console.log("Guardar objeto");
}

// Función de validación del formulario
function validateForm() {
    const usuario = document.getElementById("nombre-propietario").value;
    const fecha = document.getElementById("fecha").value;
    const contacto = document.getElementById("contacto").value;
    const descripcion = document.getElementById("descripcion").value;
    const estado = document.getElementById("estado").value;
    const clasificacion = document.getElementById("clasificacion").value;

    // Validación de longitud del usuario (exactamente 8 caracteres)
    //if (usuario.length !== 8) {
    //    alert("El usuario debe tener exactamente 8 caracteres.");
    //    return false;
    //}

    // Validación de que el campo "El objeto fue" no esté vacío
    if (!estado) {
        alert("Por favor, selecciona el estado de 'El objeto fue'.");
        return false;
    }

    // Validación de que la fecha no esté vacía
    if (!fecha) {
        alert("Por favor, selecciona una fecha.");
        return false;
    }

    // Validación de la fecha (que no sea posterior a un día después de hoy)
    const fechaSeleccionada = new Date(fecha);
    const fechaActual = new Date();
    fechaActual.setHours(0, 0, 0, 0); // Establecer solo la fecha actual sin horas
    const fechaMaxima = new Date(fechaActual);
    fechaMaxima.setDate(fechaMaxima.getDate() + 1); // Un día después de la fecha actual

    if (fechaSeleccionada > fechaMaxima) {
        alert("La fecha no puede ser posterior a un día después de la fecha actual.");
        return false;
    }

    // Validación del contacto (número telefónico)
    const contactoRegex = /^[0-9]{10}$/;
    if (!contactoRegex.test(contacto)) {
        alert("El contacto debe ser un número telefónico válido de 10 dígitos.");
        return false;
    }

    // Validación de que el campo "Clasificación" no esté vacío
    if (!clasificacion) {
        alert("Por favor, selecciona una clasificación.");
        return false;
    }

    // Validación de la descripción (máximo 280 caracteres, puede estar vacía)
    if (descripcion && descripcion.length > 280) {
        alert("La descripción debe tener un máximo de 280 caracteres.");
        return false;
    }

    // Confirmación antes de continuar con el registro
    const confirmacion = confirm("Verifica que toda la información sea correcta. Una vez que hayas registrado el objeto no podrás editarlo, ya que será enviado para su revisión.");
    if (confirmacion) {
        // Si el usuario confirma, se muestra el mensaje de éxito
        alert("Objeto registrado exitosamente.");
        return true;
    } else {
        // Si el usuario cancela, no se realiza el registro
        return false;
    }
}

const searchBar = document.getElementById("search-bar");

searchBar.addEventListener("keypress", function (event) {
    if (event.key === "Enter") { // Detecta cuando se presiona la tecla Enter
        const query = searchBar.value.trim();
        if (query) {
            // Redirige a un archivo HTML local, pasando el texto ingresado como un parámetro
            window.location.href = `BusquedaObj.html?query=${encodeURIComponent(query)}`;
        }
    }
});