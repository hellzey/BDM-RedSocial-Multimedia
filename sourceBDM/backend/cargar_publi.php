<?php
include 'conex.php';

$sql = "SELECT p.*, u.NombreC, u.Nick, u.Foto 
        FROM Publicaciones p 
        JOIN Usuarios u ON p.usuarioID = u.ID 
        ORDER BY RAND()";

$result = $conn->query($sql);

while($row = $result->fetch_assoc()):
    ?>
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
