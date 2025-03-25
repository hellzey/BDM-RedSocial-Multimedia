<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesión</title>
    <link rel="stylesheet" href="../css/iniciosesion.css">
</head>
<body> 
    <div class="container">
        <div class="login-container">
            <h3>Accede a tu cuenta</h3>
            <form action="login.php" method="POST">
                <input type="email" name="email" placeholder="Nombre de Usuario o Correo electrónico" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button type="submit">Iniciar sesión</button>
                <button onclick="location.href='registro.php'">Crear cuenta nueva</button>
            </form>
        </div>
    </div>
</body>
</html>
