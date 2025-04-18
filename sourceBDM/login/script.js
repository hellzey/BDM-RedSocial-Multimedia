document.getElementById("registroForm").addEventListener("submit", function (event) {
    event.preventDefault(); 

    // Validar campos
    let nombre = document.getElementById("nombre").value.trim();
    let apellidoPaterno = document.getElementById("apellidoPaterno")?.value.trim(); // <- Esto no está en tu HTML actual
    let apellidoMaterno = document.getElementById("apellidoMaterno")?.value.trim(); // <- Esto tampoco
    let fechaNacimiento = document.getElementById("fechaNacimiento").value;
    let nombreUsuario = document.getElementById("nombreUsuario").value.trim();
    let password = document.getElementById("password").value.trim();
    let email = document.getElementById("email").value.trim();
    let genero = document.getElementById("genero").value;
    let fileUpload = document.getElementById("file-upload").files[0];

    // Validar cada campo
    if (nombre === "") {
        alert("Por favor, ingresa tu nombre o nombres.");
        return;
    }
    // Solo validar si esos campos existen en tu HTML
    if (apellidoPaterno !== undefined && apellidoPaterno === "") {
        alert("Por favor, ingresa tu apellido paterno.");
        return;
    }
    if (apellidoMaterno !== undefined && apellidoMaterno === "") {
        alert("Por favor, ingresa tu apellido materno.");
        return;
    }
    if (email === "" || !email.includes("@")) {
        alert("Por favor, ingresa un correo electrónico válido.");
        return;
    }
    if (password === "" || password.length < 6) {
        alert("Por favor, ingresa una contraseña válida (mínimo 6 caracteres).");
        return;
    }
    if (fechaNacimiento === "") {
        alert("Por favor, ingresa tu fecha de nacimiento.");
        return;
    }
    if (nombreUsuario === "") {
        alert("Por favor, ingresa un nombre de usuario.");
        return;
    }
    if (genero === "") {
        alert("Por favor, selecciona un género.");
        return;
    }
    if (!fileUpload) {
        alert("Por favor, selecciona una foto de perfil.");
        return;
    }

    // Confirmar envío y proceder
    if (confirm("¿Estás seguro de que deseas enviar el formulario?")) {
        alert("Formulario enviado correctamente.");
        document.getElementById("registroForm").submit(); // Aquí sí se envía
    }
});

// Previsualización de la foto de perfil
document.getElementById("file-upload").addEventListener("change", function(event) {
    const file = event.target.files[0];
    if (file && file.type.startsWith("image/")) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewImage = document.getElementById("preview-image");
            const previewContainer = document.getElementById("preview-container");
            previewImage.src = e.target.result;
            previewContainer.style.display = "block";
        };
        reader.readAsDataURL(file);
    } else {
        alert("Por favor, selecciona un archivo de imagen válido.");
        document.getElementById("file-upload").value = ""; // Limpiar el campo de archivo
    }
});

// Ir a inicio de sesión
document.getElementById("iniciosesion-label").addEventListener("click", function () {
    location.href = 'iniciosesion.php';
});


document.getElementById("inicio-label").addEventListener("click", function () {
    location.href = '../content/inicio.php';
});
