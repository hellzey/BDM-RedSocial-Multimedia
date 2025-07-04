<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../css/registro.css">
    <link rel="Icon" href="../media/Freedom_Icono.png">
</head>
<body>
    <div class="container">
        <h1>Registro de Usuario</h1>
        <form id="registroForm" method="POST" action="generarRegistro.php" enctype="multipart/form-data">
            <!-- Nombre -->
            <label for="nombre">Nombre(s):</label>
            <input type="text" id="nombre" name="nombre" placeholder="Ingrese su nombre o nombres" required>

            <!-- Fecha de Nacimiento -->
            <label for="fechaNacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fechaNacimiento" name="fechaNacimiento" required>

            <!-- Nombre de Usuario -->
            <label for="nombreUsuario">Nombre de Usuario:</label>
            <input type="text" id="nombreUsuario" name="nombreUsuario" placeholder="Ingrese su nombre de usuario" required>

            <!-- Contraseña -->
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>

            <!-- Correo Electrónico -->
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" placeholder="Ingresa tu correo electrónico" required>

            <!-- Género -->
            <label for="genero">Género:</label>
            <select id="genero" name="genero" required>
                <option value="">Selecciona el género al que perteneces</option>
                <option value="masculino">Masculino</option>
                <option value="femenino">Femenino</option>
            </select>

            <!-- Foto de perfil -->
            <div class="foto-container">
                <label for="file-upload" class="file-upload-label">Subir Foto de Perfil</label>
                <input type="file" id="file-upload" name="foto" accept="image/*" style="display: none;">
            </div>
            <div id="preview-container" style="display: none;">
                <img id="preview-image" src="" alt="Previsualización" style="width: 100%; margin-top: 10px; border-radius: 5px;">
            </div>

            <!-- Botón de envío -->
            <button type="submit">Registrarse</button>
            <div class="link-container">
                <label for="iniciosesion" id="iniciosesion-label">Ya tengo una cuenta</label>
            </div>
            <div class="link-container">
                <label for="inicio" id="inicio-label">Pag inicio</label>
            </div>
        </form>
    </div>

    <script src=script.js></script>
    </script>
</body>
</html>
