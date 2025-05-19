<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesión</title>
    <link rel="stylesheet" href="../css/iniciosesion.css">
    <link rel="Icon" href="../media/Freedom_Icono.png">
</head>
<body> 
    <div class="container">
        <div class="login-container">
            <h3>Accede a tu cuenta</h3>
            <form action="login.php" method="POST">
                <input type="email" name="email" placeholder="Nombre de Usuario o Correo electrónico" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button type="submit">Iniciar sesión</button>
                <div class="link-container">
                    <label for="registro" id="registro-label">Crear cuenta nueva</label>
                </div>
                <div class="link-container">
                    <label for="inicio" id="inicio-label">Pag. Inicio</label>
                </div>
                <script>
                    document.getElementById("registro-label").addEventListener("click", function () {
                    location.href = 'registro.php'; // Redirige al registro de usuario
                    });

                    document.getElementById("inicio-label").addEventListener("click", function () {
                    location.href = '../content/inicio.php'; // Redirige al registro de usuario
                    });
                </script>
            </form>
        </div>
    </div>

</body>
</html>
