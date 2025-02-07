<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio</title>
    <link rel="stylesheet" href="css/iniciocss.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body> 

    <?php include 'nav.php';  ?>
    
    <div class="container">
        <h2>Publicaciones Recientes</h2>   
        <!-- Apartado para hacer una publicación -->
<div class="new-post-container">
    <h3>Hacer una nueva publicación</h3>
    <form>
        <textarea placeholder="¿Qué estás pensando?" rows="4"></textarea>
        <!-- Campo de archivo con etiqueta personalizada -->
        <input type="file" id="file-upload" accept="image/*,video/*">
        <label for="file-upload" class="file-upload-label">archivo</label>
        <button type="submit">Publicar</button>
    </form>
</div>
        <div class="post-container">
            <div class="post">
                <div class="user-info">
                    <div class="user-avatar">
                        <img src="media/usuario.png" alt="Avatar de 21 tremboy" class="avatar-img">
                    </div>
                    <div class="user-details">
                        <p><span class="username">21 tremboy</span> <span class="handle">@tremboy_</span> · <span class="time">2m</span></p>
                    </div>
                </div>
                <p>there’s not enough 🗣️🗣️🗣️</p>
            </div>
            <div class="post">
                <div class="user-info">
                    <div class="user-avatar">
                        <img src="media/usuario.png" alt="Avatar de cath" class="avatar-img">
                    </div>
                    <div class="user-details">
                        <p><span class="username">cath</span> <span class="handle">@knra03</span> · <span class="time">6h</span></p>
                    </div>
                </div>
                <p>My body is a machine that turns water into pee</p>
            </div>
            <div class="post">
                <div class="user-info">
                    <div class="user-avatar">
                        <img src="media/usuario.png" alt="Avatar de lu" class="avatar-img">
                    </div>
                    <div class="user-details">
                        <p><span class="username">lu</span> <span class="handle">@luna07</span> · <span class="time">1h</span></p>
                    </div>
                </div>
                <p>I can’t believe it’s already February 😭</p>
            </div>
            <div class="post">
                <div class="user-info">
                    <div class="user-avatar">
                        <img src="media/usuario.png" alt="Avatar de marco" class="avatar-img">
                    </div>
                    <div class="user-details">
                        <p><span class="username">marco</span> <span class="handle">@marco99</span> · <span class="time">3h</span></p>
                    </div>
                </div>
                <p>Me watching the same show for the 5th time 👀📺</p>
            </div>
        </div>
    </div>

</body>
</html>