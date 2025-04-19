<?php include 'nav.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio</title>
    <link rel="stylesheet" href="../css/iniciocss.css">
    <script src="../script/coment.js"></script>
</head>
<body> 

<div class="container">
    <h2>Publicaciones Recientes</h2>   
    
   

    <div class="post-container">
        <?php include '../backend/cargar_publi.php'; ?>
    </div>
</div>

<!-- Modal -->
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
        modalVideo.pause();
    });

    modal.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.style.display = "none";
            modalVideo.pause();
        }
    });
});
</script>
</body>
</html>
