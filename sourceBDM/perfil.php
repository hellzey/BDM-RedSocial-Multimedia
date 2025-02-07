<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="css/perfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    
    <?php include 'nav.php'; ?>
    <div class="profile-container">
    <div class="profile-header">
        <img src="media/griffith.jpg" alt="Foto de perfil" class="profile-img">
        <div class="profile-details">
            <p class="profile-name">Nombre del Usuario</p>
            <p class="profile-handle">@usuario</p>
        </div>
    </div>
    <p class="profile-bio">Esta es la biografía del usuario. Aquí puede escribir algo sobre sí mismo.</p>
</div>



        <div class="new-post-container">
    <h3>Hacer una nueva publicación</h3>
    <form>
        <textarea placeholder="¿Qué estás pensando?" rows="4"></textarea>
        <input type="file" id="file-upload" accept="image/*,video/*">
        <label for="file-upload" class="file-upload-label">archivo</label>
        <button type="submit">Publicar</button>
    </form>
</div>


        <h2>Publicaciones del Usuario</h2>
        <div class="post-container">
            <div class="post">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="user-details">
                        <p><span class="username">Nombre del Usuario</span> <span class="handle">@usuario</span> · <span class="time">2m</span></p>
                    </div>
                </div>
                <p>Contenido de la publicación del usuario.</p>
            </div>
            <div class="post">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="user-details">
                        <p><span class="username">Nombre del Usuario</span> <span class="handle">@usuario</span> · <span class="time">1h</span></p>
                    </div>
                </div>
                <p>Otro contenido de la publicación del usuario.</p>
            </div>
            <!-- Agrega más publicaciones del usuario aquí -->
        </div>
    </div>
    <footer>
    </footer>
</body>
</html>