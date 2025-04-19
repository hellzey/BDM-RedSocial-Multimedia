<?php
include '../backend/conex.php';
include 'nav.php';

// Consulta actualizada con nombres de campos correctos
$sql = "SELECT p.*, u.NombreC, u.Nick, u.Foto 
        FROM Publicaciones p 
        JOIN Usuarios u ON p.usuarioID = u.ID 
        ORDER BY RAND()";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio</title>
    <link rel="stylesheet" href="../css/iniciocss.css">
</head>
<body> 

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
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="post">
                <div class="user-info">
                    <div class="user-avatar">
                        <img src="<?php echo $row['Foto'] ? 'data:image/jpeg;base64,' . base64_encode($row['Foto']) : '../media/usuario.png'; ?>" class="avatar-img">
                    </div>
                    <div class="user-details">
                        <p>
                            <span class="username"><?php echo htmlspecialchars($row['NombreC']); ?></span>
                            <span class="handle">@<?php echo htmlspecialchars($row['Nick']); ?></span> · 
                            <span class="time"><?php echo date("H:i", strtotime($row['fechacreacion'])); ?></span>
                        </p>
                    </div>
                </div>
                <p><?php echo htmlspecialchars($row['descripcion']); ?></p>

                <?php
                $publiID = $row['publiID'];
                $sqlMedia = "SELECT archivo, tipo FROM MultimediaPublicaciones WHERE publiID = $publiID";
                $mediaResult = $conn->query($sqlMedia);
                ?>

                <?php if ($mediaResult->num_rows > 0): ?>
                    <div class="post-media">
                        <?php while($media = $mediaResult->fetch_assoc()): ?>
                            <?php if ($media['tipo'] == 'imagen'): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($media['archivo']); ?>" class="media-item" alt="Imagen publicación">
                            <?php elseif ($media['tipo'] == 'video'): ?>
                                <video class="media-item" controls>
                                    <source src="data:video/mp4;base64,<?php echo base64_encode($media['archivo']); ?>" type="video/mp4">
                                    Tu navegador no soporta el video.
                                </video>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
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
