<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio</title>
    <link rel="stylesheet" href="../css/iniciocss.css">

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

                <!-- Aquí se mostrarán las imágenes -->
                <div class="post-images">
                    <img src="media/griffith3.jpg" alt="Imagen 1">
                    <img src="media/Griffith2.jpg" alt="Imagen 2">
                    <img src="media/griffith.jpg" alt="Imagen 3">
                </div>
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











    <!-- Modal para vista previa de imágenes -->
<div id="imageModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const modal = document.getElementById("imageModal");
        const modalImg = document.getElementById("modalImage");
        const closeBtn = document.querySelector(".close");

        // Evento para abrir la imagen en el modal
        document.querySelectorAll(".post-images img").forEach(img => {
            img.addEventListener("click", function () {
                modal.style.display = "flex";
                modalImg.src = this.src;
            });
        });

        // Cerrar el modal al hacer clic en la "X" o fuera de la imagen
        closeBtn.addEventListener("click", () => modal.style.display = "none");
        modal.addEventListener("click", (e) => {
            if (e.target === modal) modal.style.display = "none";
        });
    });
</script>

</body>
</html>