<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'conex.php';

// Verificar si el usuario está logueado
$id_usuario = $_SESSION['id_usuario'] ?? null;
$logged_in = $id_usuario !== null;

$sql = "
    SELECT p.*, u.Nick, u.Foto, u.NombreC, u.ID as usuarioID
    FROM Publicaciones p
    INNER JOIN Usuarios u ON p.usuarioID = u.ID
    ORDER BY RAND()
";

$resultado = $conn->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $publiID = $row['publiID'];
        
        // Comprobar si el usuario actual dio like (solo si está logueado)
        $userLiked = false;
        if ($logged_in) {
            $sqlUserLike = "SELECT * FROM Reacciones WHERE publiID = $publiID AND usuarioID = $id_usuario AND tipo = 1";
            $resUserLike = $conn->query($sqlUserLike);
            $userLiked = ($resUserLike && $resUserLike->num_rows > 0);
        }
        
        // Contador de "Me gusta"
        $sqlLikes = "SELECT COUNT(*) as total FROM Reacciones WHERE publiID = $publiID AND tipo = 1";
        $likesRes = $conn->query($sqlLikes);
        $likes = $likesRes->fetch_assoc()['total'] ?? 0;
        
        // Inicio del post (publicación)
        echo '<div class="post">';
        
        // Info del usuario
        echo '<div class="user-info">';
        echo '<div class="user-avatar">';
        echo !empty($row['Foto'])
            ? '<img src="data:image/jpeg;base64,' . base64_encode($row['Foto']) . '" class="avatar-img">'
            : '<img src="../media/usuario.png" class="avatar-img">';
        echo '</div>';
        echo '<div class="user-details">';
        echo '<p>';
        echo '<span class="username">';
        echo '<a href="ver_perfil.php?id=' . $row['usuarioID'] . '">';
        echo htmlspecialchars($row['NombreC'] ?? $row['Nick']);
        echo '</a>';
        echo '</span> ';
        echo '<span class="handle">';
        echo '<a href="ver_perfil.php?id=' . $row['usuarioID'] . '">';
        echo '@' . htmlspecialchars($row['Nick']);
        echo '</a>';
        echo '</span> · ';
        echo '<span class="time">' . date("H:i d/m/y", strtotime($row['fechacreacion'])) . '</span>';
        echo '</p>';
        echo '</div></div>'; // fin user-info
        
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
        
        // Botón de like con indicación visual
        if ($logged_in) {
            $likedClass = $userLiked ? 'liked' : '';
            $likeIcon = $userLiked ? '❤️' : '🤍';
            echo '<button class="action-btn like-btn ' . $likedClass . '" onclick="toggleLike(' . $publiID . ')" id="like-btn-' . $publiID . '">' . $likeIcon . ' <span id="like-count-' . $publiID . '">' . $likes . '</span></button>';
        } else {
            // Para usuarios no logueados, mostrar botón deshabilitado
            echo '<button class="action-btn like-btn disabled" title="Inicia sesión para dar me gusta">🤍 ' . $likes . '</button>';
        }
        
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
        
        // Formulario para agregar comentario (solo para usuarios logueados)
        if ($logged_in) {
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