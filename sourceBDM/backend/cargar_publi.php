<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'conex.php';

$id_usuario = $_SESSION['id_usuario'] ?? null;

$sql = "
    SELECT p.*, u.Nick, u.Foto
    FROM Publicaciones p
    INNER JOIN Usuarios u ON p.usuarioID = u.ID
    ORDER BY RAND()
";

$resultado = $conn->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $publiID = $row['publiID'];

        echo '<div class="post">';

        // Info del usuario
        echo '<div class="user-info">';
        echo '<div class="user-avatar">';
        echo !empty($row['Foto'])
            ? '<img src="data:image/jpeg;base64,' . base64_encode($row['Foto']) . '" class="avatar-img">'
            : '<img src="img/default-profile.jpg" class="avatar-img">';
        echo '</div>';
        echo '<div class="user-details">';
        echo '<span class="username">@' . htmlspecialchars($row['Nick']) . '</span>';
        echo '<span class="time">' . date("d/m/Y H:i", strtotime($row['fechacreacion'])) . '</span>';
        echo '</div></div>'; // fin user-info

        // Contenido de la publicación
        echo '<div class="post-content">';
        echo '<p>' . htmlspecialchars($row['descripcion']) . '</p>';

        // Multimedia
        $sqlMultimedia = "SELECT * FROM MultimediaPublicaciones WHERE publiID = $publiID";
        $resultadoMultimedia = $conn->query($sqlMultimedia);

        if ($resultadoMultimedia && $resultadoMultimedia->num_rows > 0) {
            echo '<div class="post-media">';
            while ($media = $resultadoMultimedia->fetch_assoc()) {
                if ($media['tipo'] === 'imagen') {
                    echo '<img class="media-item" src="data:image/jpeg;base64,' . base64_encode($media['archivo']) . '">';
                } elseif ($media['tipo'] === 'video') {
                    echo '<video class="media-item" controls><source src="data:video/mp4;base64,' . base64_encode($media['archivo']) . '" type="video/mp4"></video>';
                }
            }
            echo '</div>';
        }
        echo '</div>'; // fin post-content

        // Botón para mostrar comentarios
        echo '<div class="post-actions">';
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

        // Formulario para comentar (solo si el usuario está logueado)
        if ($id_usuario) {
            echo '<form class="comment-form" onsubmit="enviarComentario(event, ' . $publiID . ')">';
            echo '<input type="text" name="comentario" placeholder="Escribe un comentario..." required>';
            echo '<button type="submit">Enviar</button>';
            echo '</form>';
        } else {
            echo '<p class="login-hint">Inicia sesión para comentar.</p>';
        }

        echo '</div>'; // fin comments-section
        echo '</div>'; // fin post
    }
} else {
    echo '<div class="no-posts">No hay publicaciones disponibles.</div>';
}
?>
