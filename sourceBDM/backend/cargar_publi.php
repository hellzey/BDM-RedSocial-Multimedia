<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'conex.php';

$id_usuario = $_SESSION['id_usuario'];

$sql = "
    SELECT p.*, u.Nick, u.Foto
    FROM Publicaciones p
    INNER JOIN Usuarios u ON p.usuarioID = u.ID
    ORDER BY p.fechacreacion DESC
";

$resultado = $conn->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $publiID = $row['publiID'];
        
        // Comprobar si el usuario actual ya dio like a esta publicación
        $sqlUserLike = "SELECT * FROM Reacciones WHERE publiID = $publiID AND usuarioID = $id_usuario AND tipo = 1";
        $resUserLike = $conn->query($sqlUserLike);
        $userLiked = ($resUserLike && $resUserLike->num_rows > 0);
        
        // Contador de "Me gusta"
        $sqlLikes = "SELECT COUNT(*) as total FROM Reacciones WHERE publiID = $publiID AND tipo = 1";
        $likesRes = $conn->query($sqlLikes);
        $likes = $likesRes->fetch_assoc()['total'] ?? 0;
        
        // Inicio del post (publicación)
        echo '<div class="post">';
        
        // Información del usuario
        echo '<div class="user-info">';
        echo '<div class="user-avatar">';
        if (!empty($row['Foto'])) {
            echo '<img src="data:image/jpeg;base64,' . base64_encode($row['Foto']) . '" class="avatar-img">';
        } else {
            echo '<img src="img/default-profile.jpg" class="avatar-img">';
        }
        echo '</div>'; // fin user-avatar
        
        echo '<div class="user-details">';
        echo '<span class="username">@' . htmlspecialchars($row['Nick']) . '</span>';
        echo '<span class="time">' . date("d/m/Y H:i", strtotime($row['fechacreacion'])) . '</span>';
        echo '</div>'; // fin user-details
        echo '</div>'; // fin user-info
        
        // Contenido de la publicación
        echo '<div class="post-content">';
        echo '<p>' . htmlspecialchars($row['descripcion']) . '</p>';
        
        // Mostrar multimedia
        $sqlMultimedia = "SELECT * FROM MultimediaPublicaciones WHERE publiID = $publiID";
        $resultadoMultimedia = $conn->query($sqlMultimedia);
        
        if ($resultadoMultimedia && $resultadoMultimedia->num_rows > 0) {
            echo '<div class="post-media">';
            while ($media = $resultadoMultimedia->fetch_assoc()) {
                if ($media['tipo'] == 'imagen') {
                    echo '<img class="media-item" src="data:image/jpeg;base64,' . base64_encode($media['archivo']) . '">';
                } elseif ($media['tipo'] == 'video') {
                    echo '<video class="media-item" controls><source src="data:video/mp4;base64,' . base64_encode($media['archivo']) . '" type="video/mp4"></video>';
                }
            }
            echo '</div>'; // fin post-media
        }
        echo '</div>'; // fin post-content
        
        // Sección de acciones (likes y comentarios)
        echo '<div class="post-actions">';
        
        // Botón de like con indicación visual si el usuario ya dio like
        $likedClass = $userLiked ? 'liked' : '';
        echo '<button class="action-btn like-btn ' . $likedClass . '" data-publi="' . $publiID . '">❤️ Me gusta <span class="likes-counter">(' . $likes . ')</span></button>';
        
        echo '<button class="action-btn comment-btn" onclick="mostrarComentarios(' . $publiID . ')">💬 Comentarios</button>';
        echo '</div>'; // fin post-actions
        
        // Obtener comentarios
        $sqlComentarios = "
            SELECT c.*, u.Nick 
            FROM Comentarios c
            JOIN Usuarios u ON c.usuarioID = u.ID
            WHERE c.publiID = $publiID
            ORDER BY c.fecha_comentario DESC
            LIMIT 3
        ";
        $resComentarios = $conn->query($sqlComentarios);
        
        // Sección de comentarios
        echo '<div class="comments-section" id="comentarios-' . $publiID . '" style="display:none;">';
        
        if ($resComentarios && $resComentarios->num_rows > 0) {
            echo '<div class="comments-list">';
            while ($coment = $resComentarios->fetch_assoc()) {
                echo '<div class="comment">';
                echo '<span class="comment-username">@' . htmlspecialchars($coment['Nick']) . ':</span> ';
                echo '<span class="comment-text">' . htmlspecialchars($coment['comentario']) . '</span>';
                echo '</div>';
            }
            echo '</div>'; // fin comments-list
        } else {
            echo '<p class="no-comments">No hay comentarios todavía.</p>';
        }
        
        // Formulario para agregar comentario
        echo '<form class="comment-form" onsubmit="enviarComentario(event, ' . $publiID . ')">';
        echo '<input type="text" name="comentario" placeholder="Escribe un comentario..." required>';
        echo '<button type="submit">Enviar</button>';
        echo '</form>';
        
        echo '</div>'; // fin comments-section
        
        echo '</div>'; // fin post
    }
} else {
    echo '<div class="no-posts">No hay publicaciones disponibles.</div>';
}
?>