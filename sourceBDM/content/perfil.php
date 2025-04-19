<?php
session_start();
require_once '../backend/conex.php';
require_once '../backend/usuario_info.php';
require_once '../backend/publicar.php';
require_once '../backend/perfil_publi.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: iniciosesion.php");
    exit();
}

$idUsuario = $_SESSION['id_usuario'];

// Obtener datos del usuario
$usuario = obtenerInfoUsuario($conn, $idUsuario);

if (!$usuario) {
    echo "Usuario no encontrado";
    exit();
}

$nombre = $usuario['NombreC'];
$nick = $usuario['Nick'];
$seguidores = $usuario['N_seguidores'];
$biografia = $usuario['Biografia'];
$foto = $usuario['Foto'];

// Procesar la publicación si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['publicar'])) {
    $descripcion = $_POST['descripcion'];
    $archivos = $_FILES['archivo'];
    
    if (procesarPublicacion($conn, $descripcion, $idUsuario, $archivos)) {
        header("Location: perfil.php");
        exit();
    } else {
        // Manejar error en la publicación
        $error_mensaje = "Hubo un error al procesar tu publicación. Por favor, intenta nuevamente.";
    }
}

// Obtener publicaciones del usuario
$publicaciones = obtenerPublicacionesUsuario($conn, $idUsuario);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="../css/perfil.css">
    <script src="../script/coment.js"></script>
    <script src="../script/like.js"></script>
</head>
<body>
<?php include 'nav.php'; ?>
<div class="profile-container">
    <div class="profile-header">
        <img src="<?php echo $foto; ?>" alt="Foto de perfil" class="profile-img">
        <div class="profile-details">
            <p class="profile-name"><?php echo htmlspecialchars($nombre); ?></p>
            <p class="profile-handle">@<?php echo htmlspecialchars($nick); ?></p>
        </div>
    </div>

    <p class="profile-bio"><?php echo htmlspecialchars($biografia ?: "Este usuario aún no ha escrito su biografía."); ?></p>

    <p class="followers-count">
    <a href="followers.php"><?php echo $seguidores; ?> Seguidores</a><br>
    <a href="edit_perfil.php">Editar perfil</a>
</p>
</div>

<!-- Formulario de Publicación -->
<div class="new-post-container">
    <h3>Hacer una nueva publicación</h3>
    <?php if (isset($error_mensaje)): ?>
        <div class="error-message"><?php echo $error_mensaje; ?></div>
    <?php endif; ?>
    <form action="perfil.php" method="POST" enctype="multipart/form-data">
        <textarea name="descripcion" placeholder="¿Qué estás pensando?" rows="4"></textarea>
        <input type="file" name="archivo[]" id="file-upload" accept="image/*,video/*" multiple>
        <label for="file-upload" class="file-upload-label">archivo</label>

        <!-- Sección de previsualización -->
        <div id="preview-container"></div>

        <button type="submit" name="publicar">Publicar</button>
    </form>
</div>

<script>
    document.getElementById('file-upload').addEventListener('change', function(event) {
        const files = event.target.files;
        const previewContainer = document.getElementById('preview-container');
        previewContainer.innerHTML = '';  // Limpiar las previsualizaciones anteriores

        // Recorrer todos los archivos seleccionados
        Array.from(files).forEach(file => {
            const fileReader = new FileReader();

            fileReader.onload = function(e) {
                const fileType = file.type;

                // Crear el elemento de previsualización
                const previewElement = document.createElement('div');
                previewElement.classList.add('file-preview');

                if (fileType.startsWith('image')) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Imagen de previsualización';
                    img.classList.add('preview-img');
                    previewElement.appendChild(img);
                } else if (fileType.startsWith('video')) {
                    const video = document.createElement('video');
                    video.controls = true;
                    video.src = e.target.result;
                    video.classList.add('preview-video');
                    previewElement.appendChild(video);
                }

                previewContainer.appendChild(previewElement);
            };

            // Leer el archivo seleccionado
            fileReader.readAsDataURL(file);
        });
    });
</script>

<<!-- Mostrar publicaciones -->
<h2>Publicaciones del Usuario</h2>
<div class="post-container">
<?php
foreach ($publicaciones as $publi) {
    $publiID = $publi['publiID'];

    // Verificar si el usuario actual ya dio like
    $sqlUserLike = "SELECT 1 FROM Reacciones WHERE publiID = $publiID AND usuarioID = $idUsuario AND tipo = 1";
    $resUserLike = $conn->query($sqlUserLike);
    $userLiked = ($resUserLike && $resUserLike->num_rows > 0);

    // Contador de "Me gusta"
    $sqlLikes = "SELECT COUNT(*) as total FROM Reacciones WHERE publiID = $publiID AND tipo = 1";
    $likesRes = $conn->query($sqlLikes);
    $likes = $likesRes->fetch_assoc()['total'] ?? 0;

    echo "<div class='post'>";
    echo "<div class='user-info'>";
    echo "<div class='user-avatar'>";
    echo "<img src='" . $foto . "' alt='Avatar de " . htmlspecialchars($nombre) . "' class='avatar-img'>";
    echo "</div>";
    echo "<div class='user-details'>";
    echo "<p><span class='username'>" . htmlspecialchars($nombre) . "</span> <span class='handle'>@" . htmlspecialchars($nick) . "</span> · <span class='time'>" . $publi['fechacreacion'] . "</span></p>";
    echo "</div></div>";

    echo "<p>" . htmlspecialchars($publi['descripcion']) . "</p>";

    if (!empty($publi['multimedia'])) {
        echo "<div class='post-media'>";
        foreach ($publi['multimedia'] as $media) {
            $archivo = base64_encode($media['archivo']);
            if ($media['tipo'] === 'imagen') {
                echo "<img src='data:image/jpeg;base64,{$archivo}' alt='Imagen de publicación' class='media-item'>";
            } elseif ($media['tipo'] === 'video') {
                echo "<video controls class='media-item'><source src='data:video/mp4;base64,{$archivo}' type='video/mp4'></video>";
            }
        }
        echo "</div>";
    }

    // Sección de acciones
    echo '<div class="post-actions">';
    $likedClass = $userLiked ? 'liked' : '';
    echo '<button id="like-btn-' . $publiID . '" class="action-btn like-btn ' . $likedClass . '" onclick="toggleLike(' . $publiID . ')">❤️ <span id="like-count-' . $publiID . '">' . $likes . '</span></button>';

    echo '<button class="action-btn comment-btn" onclick="mostrarComentarios(' . $publiID . ')">💬 Comentarios</button>';
    echo '</div>';

    // Comentarios
    $sqlComentarios = "
        SELECT c.*, u.Nick 
        FROM Comentarios c
        JOIN Usuarios u ON c.usuarioID = u.ID
        WHERE c.publiID = $publiID
        ORDER BY c.fecha_comentario DESC
        LIMIT 3
    ";
    $resComentarios = $conn->query($sqlComentarios);

    echo '<div class="comments-section" id="comentarios-' . $publiID . '" style="display:none;">';
    if ($resComentarios && $resComentarios->num_rows > 0) {
        echo '<div class="comments-list">';
        while ($coment = $resComentarios->fetch_assoc()) {
            echo '<div class="comment">';
            echo '<span class="comment-username">@' . htmlspecialchars($coment['Nick']) . ':</span> ';
            echo '<span class="comment-text">' . htmlspecialchars($coment['comentario']) . '</span>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p class="no-comments">No hay comentarios todavía.</p>';
    }

    // Formulario para comentar
    echo '<form class="comment-form" onsubmit="enviarComentario(event, ' . $publiID . ')">';
    echo '<input type="text" name="comentario" placeholder="Escribe un comentario..." required>';
    echo '<button type="submit">Enviar</button>';
    echo '</form>';
    echo '</div>'; // fin comentarios
    echo '</div>'; // fin post
}
?>
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

<!-- Modal para multimedia -->
<div id="mediaModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="modalImage">
    <video class="modal-content" id="modalVideo" controls></video>
</div>
</body>
</html>