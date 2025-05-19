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

// Procesar eliminación de publicación
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_publicacion'])) {
    $publiID = intval($_POST['publi_id']);

    // Verifica que la publicación pertenezca al usuario antes de eliminarla
    $stmt = $conn->prepare("DELETE FROM Publicaciones WHERE publiID = ? AND usuarioID = ?");
    $stmt->bind_param("ii", $publiID, $idUsuario);
    $stmt->execute();

    // También puedes eliminar multimedia asociada, reacciones, comentarios, etc., si lo deseas

    header("Location: perfil.php");
    exit();
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
    <link rel="Icon" href="../media/Freedom_Icono.png">
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
    <a href="followers.php"><?php echo $seguidores; ?> Seguidores</a><br><p>
    <a href="edit_perfil.php">Editar perfil</a><br>
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
        <label for="file-upload" class="file-upload-label">File</label>
        <!-- Sección de previsualización -->
        <div id="preview-container"></div>
        <button type="submit" name="publicar">Publicar</button>
    </form><br>

    <!-- Generar reporte de publicaciones --> 
     <div class="report-buttons">
       <h3>Reporte de mis publicaciones</h3>
       <form action="../backend/reporte.php" method="GET" style="display:inline;">
       <input type="hidden" name="usuarioID" value="<?php echo $idUsuario; ?>">
       <button type="submit" name="formato" value="csv">Descargar CSV</button>
       </form>
       <form action="../backend/reporte.php" method="GET" style="display:inline;">
       <input type="hidden" name="usuarioID" value="<?php echo $idUsuario; ?>">
       <button type="submit" name="formato" value="pdf">Descargar PDF</button>
       </form>
     </div>

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

// Botón eliminar solo para el dueño de la publicación
if ($publi['usuarioID'] == $idUsuario) {
    echo '<form action="perfil.php" method="POST" onsubmit="return confirm(\'¿Estás seguro de que deseas eliminar esta publicación?\');" style="display:inline;">';
    echo '<input type="hidden" name="publi_id" value="' . $publiID . '">';
    echo '<button type="submit" name="eliminar_publicacion" class="action-btn delete-btn">🗑️ Eliminar</button>';
    echo '</form>';
}
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

<script>
// Función para abrir el modal de edición
function abrirEdicion(publiID) {
    // Obtener los datos de la publicación mediante AJAX
    fetch(`../backend/obtener_publicacion.php?publiID=${publiID}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Llenar el formulario con los datos
                document.getElementById('editPubliID').value = publiID;
                document.getElementById('editDescripcion').value = data.publicacion.descripcion;
                
                // Mostrar los medios existentes
                const mediaContainer = document.getElementById('editMediaContainer');
                mediaContainer.innerHTML = '';
                
                if (data.multimedia && data.multimedia.length > 0) {
                    mediaContainer.innerHTML = '<h4>Medios existentes:</h4>';
                    
                    data.multimedia.forEach(media => {
                        const mediaDiv = document.createElement('div');
                        mediaDiv.className = 'existing-media';
                        
                        if (media.tipo === 'imagen') {
                            mediaDiv.innerHTML = `
                                <img src="data:image/jpeg;base64,${media.archivo}" class="preview-media">
                                <label>
                                    <input type="checkbox" name="eliminar_media[]" value="${media.ID}">
                                    Eliminar
                                </label>
                            `;
                        } else if (media.tipo === 'video') {
                            mediaDiv.innerHTML = `
                                <video controls class="preview-media">
                                    <source src="data:video/mp4;base64,${media.archivo}" type="video/mp4">
                                </video>
                                <label>
                                    <input type="checkbox" name="eliminar_media[]" value="${media.ID}">
                                    Eliminar
                                </label>
                            `;
                        }
                        
                        mediaContainer.appendChild(mediaDiv);
                    });
                }
                
                // Mostrar el modal
                document.getElementById('editModal').style.display = 'flex';
            } else {
                alert('Error al cargar la publicación: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar la publicación');
        });
}

// Función para cerrar el modal de edición
function cerrarEdicion() {
    document.getElementById('editModal').style.display = 'none';
}

// Cerrar modal al hacer clic fuera del contenido
window.onclick = function(event) {
    const modal = document.getElementById('editModal');
    if (event.target === modal) {
        cerrarEdicion();
    }
}
</script>

</body>
</html>