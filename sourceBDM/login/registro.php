<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../css/registro.css">
</head>
<body>
<?php include '../content/nav.php';  ?>
    <div class="register-container">
        <h2>Registro de Usuario</h2>
        <form>
            <input type="text" placeholder="Nombre de Usuario" required>
            <input type="email" placeholder="Correo Electrónico" required>
            <input type="password" placeholder="Contraseña" required>
            
            <!-- Campo de Fecha -->
            <input type="date" id="fecha" name="fecha" required>
            
            <label for="file-upload" class="file-upload-label">Subir Foto de Perfil</label>
            <input type="file" id="file-upload" accept="image/*" style="display: none;">
            <button type="submit">Registrarse</button>
        </form>
    </div>
</body>
</html>
