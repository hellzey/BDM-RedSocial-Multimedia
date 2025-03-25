<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../css/registro.css">
</head>
<body>
    <div class="container">
        <h1>Registro de Usuario</h1>
        <form id="registroForm">
            <!-- Nombre -->
            <label for="nombre">Nombre(s):</label>
            <input type="text" id="nombre" name="nombre" placeholder="Ingrese su nombre o nombres">
            <!-- Apellido Paterno -->
            <label for="apellidoPaterno">Apellido Paterno:</label>
            <input type="text" id="apellidoPaterno" name="apellidoPaterno" placeholder="Ingrese su apellido paterno">
            <!-- Apellido Materno -->
            <label for="apellidoMaterno">Apellido Materno:</label>
            <input type="text" id="apellidoMaterno" name="apellidoMaterno" placeholder="Ingrese su apellido materno">
            <!-- Fecha de Nacimiento -->
            <label for="fechaNacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fechaNacimiento" name="fechaNacimiento">
            <!-- NOmbre de Usuario -->
            <label for="nombreUsuario">Nombre de Usuario:</label>
            <input type="text" id="nombreUsuario" name="nombreUsuario" placeholder="Ingrese su nombre de usuario">
            <!-- Contraseña -->
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" placeholder="Ingrese su contraseña">
            <!-- Correo Electrónico -->
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" placeholder="Ingresa tu correo electrónico">
            <!-- Género -->
            <label for="genero">Género:</label>
            <select id="genero" name="genero">
                <option value="">Selecciona el género al que perteneces</option>
                <option value="masculino">Masculino</option>
                <option value="femenino">Femenino</option>
            </select>
            <!-- Foto de perfil -->
            <label for="file-upload" class="file-upload-label">Subir Foto de Perfil</label>
            <input type="file" id="file-upload" accept="image/*" style="display: none;">
            <!-- Botón de envío -->
            <button type="submit">Registrarse</button>
        </form>
    </div>

    <script src="script.js"></script>
</body>
</html>
