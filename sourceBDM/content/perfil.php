<?php
session_start();
require_once '../backend/conex.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: iniciosesion.php");
    exit();
}

$idUsuario = $_SESSION['id_usuario'];

// Obtener datos del usuario
$sql = "SELECT NombreC, Nick, Genero, Email, Fecha_Nac, Foto, N_seguidores, Biografia FROM Usuarios WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();

    $nombre = $usuario['NombreC'];
    $nick = $usuario['Nick'];
    $seguidores = $usuario['N_seguidores'];
    $biografia = $usuario['Biografia'];
    $foto = $usuario['Foto'] ? 'data:image/jpeg;base64,' . base64_encode($usuario['Foto']) : '../media/default.jpg';
} else {
    echo "Usuario no encontrado";
    exit();
}
// Procesar la publicación si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['publicar'])) {
    $descripcion = $_POST['descripcion'];
    $usuarioID = $idUsuario;
    $archivos = $_FILES['archivo'];

    // Detectar categorías (hashtags)
    preg_match_all('/#(\w+)/', $descripcion, $matches);
    $categorias = $matches[1];

    // Insertar la publicación
    $sql_pub = "INSERT INTO Publicaciones (descripcion, usuarioID, estatus) VALUES (?, ?, 1)";
    $stmt_pub = $conn->prepare($sql_pub);
    $stmt_pub->bind_param("si", $descripcion, $usuarioID);
    $stmt_pub->execute();
    $publiID = $stmt_pub->insert_id;

    // Insertar las categorías si no existen
    foreach ($categorias as $categoria) {
        // Verificar si la categoría ya existe
        $sql_cat = "INSERT INTO Categorias (categoria) SELECT ? WHERE NOT EXISTS (SELECT 1 FROM Categorias WHERE categoria = ?)";
        $stmt_cat = $conn->prepare($sql_cat);
        $stmt_cat->bind_param("ss", $categoria, $categoria);
        $stmt_cat->execute();

        // Obtener el ID de la categoría
        $sql_get_cat = "SELECT categoriaID FROM Categorias WHERE categoria = ?";
        $stmt_get_cat = $conn->prepare($sql_get_cat);
        $stmt_get_cat->bind_param("s", $categoria);
        $stmt_get_cat->execute();
        $resultado_cat = $stmt_get_cat->get_result();
        $categoriaID = $resultado_cat->fetch_assoc()['categoriaID'];

        // Asociar la categoría con la publicación
        $sql_assoc = "INSERT INTO Publicaciones_Categorias (publiID, categoriaID) VALUES (?, ?)";
        $stmt_assoc = $conn->prepare($sql_assoc);
        $stmt_assoc->bind_param("ii", $publiID, $categoriaID);
        $stmt_assoc->execute();
    }

    // Procesar los archivos multimedia
    if (!empty($archivos['name'][0])) {
        foreach ($archivos['tmp_name'] as $key => $tmp_name) {
            $file_tmp = $archivos['tmp_name'][$key];
            $file_type = $archivos['type'][$key];

            if (strpos($file_type, 'image') !== false) {
                $tipo = 'imagen';
            } elseif (strpos($file_type, 'video') !== false) {
                $tipo = 'video';
            } else {
                continue;
            }

            $file_data = file_get_contents($file_tmp);

            $sql_media = "INSERT INTO MultimediaPublicaciones (publiID, tipo, archivo) VALUES (?, ?, ?)";
            $stmt_media = $conn->prepare($sql_media);
            $stmt_media->bind_param("iss", $publiID, $tipo, $file_data);
            $stmt_media->execute();
        }
    }

    header("Location: perfil.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="../css/perfil.css">
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
        <a href="followers.php"><?php echo $seguidores; ?> Seguidores</a><br>
        <a href="edit_perfil.php">Editar perfil</a>
    </p>
</div>

<!-- Formulario de Publicación -->
<div class="new-post-container">
    <h3>Hacer una nueva publicación</h3>
    <form action="perfil.php" method="POST" enctype="multipart/form-data">
        <textarea name="descripcion" placeholder="¿Qué estás pensando?" rows="4"></textarea>
        <input type="file" name="archivo[]" id="file-upload" accept="image/*,video/*" multiple>
        <label for="file-upload" class="file-upload-label">archivo</label>
        <button type="submit" name="publicar">Publicar</button>
    </form>
</div>

<!-- Mostrar publicaciones -->
<h2>Publicaciones del Usuario</h2>
<div class="post-container">
    <?php
    $sql_pub_user = "SELECT p.publiID, p.descripcion, p.fechacreacion, m.tipo, m.archivo 
                     FROM Publicaciones p
                     LEFT JOIN MultimediaPublicaciones m ON p.publiID = m.publiID
                     WHERE p.usuarioID = ? ORDER BY p.fechacreacion DESC";
    $stmt_pub_user = $conn->prepare($sql_pub_user);
    $stmt_pub_user->bind_param("i", $idUsuario);
    $stmt_pub_user->execute();
    $result_pub_user = $stmt_pub_user->get_result();

    $publicaciones = [];

    while ($row = $result_pub_user->fetch_assoc()) {
        $id = $row['publiID'];
        if (!isset($publicaciones[$id])) {
            $publicaciones[$id] = [
                'descripcion' => $row['descripcion'],
                'fechacreacion' => $row['fechacreacion'],
                'multimedia' => []
            ];
        }

        if ($row['tipo']) {
            $publicaciones[$id]['multimedia'][] = [
                'tipo' => $row['tipo'],
                'archivo' => $row['archivo']
            ];
        }
    }

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

