<?php
session_start();
require_once '../backend/conex.php';
require_once '../backend/usuario_info.php';
require_once '../backend/perfil_publi.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: iniciosesion.php");
    exit();
}

$usuario_actual = $_SESSION['id_usuario'];

// Verificar si se proporcionó un ID de usuario
if (!isset($_GET['id'])) {
    header("Location: inicio.php");
    exit();
}

$idUsuario = $_GET['id'];

// Si el usuario está viendo su propio perfil, redirigir a perfil.php
if ($idUsuario == $usuario_actual) {
    header("Location: perfil.php");
    exit();
}

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

// Verificar si el usuario actual sigue al usuario del perfil
function verificarSeguimiento($conn, $seguidor, $seguido) {
    $sql = "SELECT * FROM Seguidores WHERE SeguidorID = ? AND SeguidoID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $seguidor, $seguido);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

$siguiendo = verificarSeguimiento($conn, $usuario_actual, $idUsuario);

// Procesar acción de seguir/dejar de seguir
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion_seguir'])) {
    if ($_POST['accion_seguir'] == 'seguir') {
        // Seguir al usuario
        $sql = "INSERT INTO Seguidores (SeguidorID, SeguidoID) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $usuario_actual, $idUsuario);
        $stmt->execute();
        
        // Actualizar contador de seguidores
        $sql = "UPDATE Usuarios SET N_seguidores = N_seguidores + 1 WHERE ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        
        $siguiendo = true;
        $seguidores++;
    } elseif ($_POST['accion_seguir'] == 'dejar_seguir') {
        // Dejar de seguir al usuario
        $sql = "DELETE FROM Seguidores WHERE SeguidorID = ? AND SeguidoID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $usuario_actual, $idUsuario);
        $stmt->execute();
        
        // Actualizar contador de seguidores
        $sql = "UPDATE Usuarios SET N_seguidores = N_seguidores - 1 WHERE ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        
        $siguiendo = false;
        $seguidores--;
    }
    
    // Redireccionar para evitar reenvío de formulario
    header("Location: ver_perfil.php?id=" . $idUsuario);
    exit();
}

// Obtener publicaciones del usuario
$publicaciones = obtenerPublicacionesUsuario($conn, $idUsuario);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de <?php echo htmlspecialchars($nombre); ?></title>
    <link rel="stylesheet" href="../css/verperfil.css">
    <script src="../js/verpubli.js"></script>
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
        <?php echo $seguidores; ?> Seguidores
    </p>
    
    <!-- Botón de seguir/dejar de seguir -->
    <div class="follow-action">
        <form method="POST" action="ver_perfil.php?id=<?php echo $idUsuario; ?>">
            <?php if ($siguiendo): ?>
                <input type="hidden" name="accion_seguir" value="dejar_seguir">
                <button type="submit" class="unfollow-btn">Dejar de seguir</button>
            <?php else: ?>
                <input type="hidden" name="accion_seguir" value="seguir">
                <button type="submit" class="follow-btn">Seguir</button>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Mostrar publicaciones -->
<h2>Publicaciones de <?php echo htmlspecialchars($nombre); ?></h2>
<div class="post-container">
    <?php
    if (empty($publicaciones)) {
        echo "<p class='no-posts'>Este usuario aún no tiene publicaciones.</p>";
    } else {
        foreach ($publicaciones as $publi) {
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

            echo "</div>";
        }
    }
    ?>
</div>

<footer></footer>

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

<!-- Modal para multimedia -->
<div id="mediaModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="modalImage">
    <video class="modal-content" id="modalVideo" controls></video>
</div>
</body>
</html>