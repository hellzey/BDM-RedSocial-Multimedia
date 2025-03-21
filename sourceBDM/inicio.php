<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio</title>
    <link rel="stylesheet" href="css/iniciocss.css">

</head>
<body> 

    <?php include 'nav.php';  ?>
    
    <div class="container">
        <h2>Publicaciones Recientes</h2>   
        
<div class="new-post-container">
    <h3>Hacer una nueva publicación</h3>
    <form>
        <textarea placeholder="¿Qué estás pensando?" rows="4"></textarea>
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
    <div class="post-media">
        <img src="media/griffith3.jpg" alt="Imagen 1" class="media-item">
        <img src="media/Griffith2.jpg" alt="Imagen 2" class="media-item">
        <video class="media-item" controls>
            <source src="media/koro.mp4" type="video/mp4">
            Tu navegador no soporta la reproducción de video.
        </video>
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
                <p>My body is a machine that turns water into pee  #foryou</p>
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









<!-- Modal para vista previa de imágenes y videos -->
<div id="mediaModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="modalImage">
    <video class="modal-content" id="modalVideo" controls></video>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const modal = document.getElementById("mediaModal");
        const modalImg = document.getElementById("modalImage");
        const modalVideo = document.getElementById("modalVideo");
        const closeBtn = document.querySelector(".close");

        document.querySelectorAll(".media-item").forEach(media => {
            media.addEventListener("click", function () {
                modal.style.display = "flex";

                if (this.tagName === "IMG") {
                    modalImg.src = this.src;
                    modalImg.style.display = "block";
                    modalVideo.style.display = "none";
                } else if (this.tagName === "VIDEO") {
                    modalVideo.src = this.querySelector("source").src;
                    modalVideo.style.display = "block";
                    modalImg.style.display = "none";
                }
            });
        });

        closeBtn.addEventListener("click", () => {
            modal.style.display = "none";
            modalVideo.pause(); // Pausar el video al cerrar el modal
        });

        modal.addEventListener("click", (e) => {
            if (e.target === modal) {
                modal.style.display = "none";
                modalVideo.pause();
            }
        });
    });
</script>
