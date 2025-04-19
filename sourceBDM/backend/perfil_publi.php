<?php
function obtenerPublicacionesUsuario($conn, $idUsuario) {
    // Obtenemos información del usuario logueado si existe
    $id_usuario_actual = $_SESSION['id_usuario'] ?? null;
    $logged_in = $id_usuario_actual !== null;
    
    // Consulta para obtener publicaciones del usuario específico
    $sql = "
        SELECT p.*, u.Nick, u.Foto, u.NombreC, u.ID as usuarioID
        FROM Publicaciones p
        INNER JOIN Usuarios u ON p.usuarioID = u.ID
        WHERE p.usuarioID = ?
        ORDER BY p.fechacreacion DESC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $publicaciones = [];
    
    if ($resultado && $resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $publiID = $row['publiID'];
            $publicacion = [
                'publiID' => $publiID,
                'descripcion' => $row['descripcion'],
                'fechacreacion' => $row['fechacreacion'],
                'Nick' => $row['Nick'],
                'NombreC' => $row['NombreC'],
                'Foto' => $row['Foto'],
                'usuarioID' => $row['usuarioID'],
                'multimedia' => [],
                'likes' => 0,
                'userLiked' => false,
                'comentarios' => []
            ];
            
            // Obtenemos el multimedia de cada publicación
            $sqlMultimedia = "SELECT * FROM MultimediaPublicaciones WHERE publiID = ?";
            $stmtMultimedia = $conn->prepare($sqlMultimedia);
            $stmtMultimedia->bind_param("i", $publiID);
            $stmtMultimedia->execute();
            $resultadoMultimedia = $stmtMultimedia->get_result();
            
            if ($resultadoMultimedia && $resultadoMultimedia->num_rows > 0) {
                while ($media = $resultadoMultimedia->fetch_assoc()) {
                    $publicacion['multimedia'][] = [
                        'tipo' => $media['tipo'],
                        'archivo' => $media['archivo']
                    ];
                }
            }
            
            // Comprobar si el usuario actual dio like (solo si está logueado)
            if ($logged_in) {
                $sqlUserLike = "SELECT * FROM Reacciones WHERE publiID = ? AND usuarioID = ? AND tipo = 1";
                $stmtUserLike = $conn->prepare($sqlUserLike);
                $stmtUserLike->bind_param("ii", $publiID, $id_usuario_actual);
                $stmtUserLike->execute();
                $resUserLike = $stmtUserLike->get_result();
                $publicacion['userLiked'] = ($resUserLike && $resUserLike->num_rows > 0);
            }
            
            // Contador de "Me gusta"
            $sqlLikes = "SELECT COUNT(*) as total FROM Reacciones WHERE publiID = ? AND tipo = 1";
            $stmtLikes = $conn->prepare($sqlLikes);
            $stmtLikes->bind_param("i", $publiID);
            $stmtLikes->execute();
            $likesRes = $stmtLikes->get_result();
            $publicacion['likes'] = $likesRes->fetch_assoc()['total'] ?? 0;
            
            // Obtener comentarios recientes usando los nombres correctos de las columnas
            $sqlComentarios = "
                SELECT c.id_comentario, c.usuarioID, c.comentario, c.fecha_comentario, u.Nick 
                FROM Comentarios c
                JOIN Usuarios u ON c.usuarioID = u.ID
                WHERE c.publiID = ?
                ORDER BY c.fecha_comentario DESC
                LIMIT 3
            ";
            $stmtComentarios = $conn->prepare($sqlComentarios);
            $stmtComentarios->bind_param("i", $publiID);
            $stmtComentarios->execute();
            $resComentarios = $stmtComentarios->get_result();
            
            if ($resComentarios && $resComentarios->num_rows > 0) {
                while ($coment = $resComentarios->fetch_assoc()) {
                    $publicacion['comentarios'][] = [
                        'id_comentario' => $coment['id_comentario'],
                        'usuarioID' => $coment['usuarioID'],
                        'Nick' => $coment['Nick'],
                        'comentario' => $coment['comentario'],
                        'fecha_comentario' => $coment['fecha_comentario']
                    ];
                }
            }
            
            $publicaciones[] = $publicacion;
        }
    }
    
    return $publicaciones;
}

// Función para mostrar las publicaciones con el mismo formato que la página de inicio
function mostrarPublicacionesUsuario($publicaciones, $logged_in = false) {
    global $conn;
    
    if (empty($publicaciones)) {
        echo '<div class="no-posts">Este usuario aún no tiene publicaciones.</div>';
        return;
    }
    
    foreach ($publicaciones as $publicacion) {
        $publiID = $publicacion['publiID'];
        $userLiked = $publicacion['userLiked'];
        $likes = $publicacion['likes'];
        
        // Inicio del post (publicación)
        echo '<div class="post">';
        
        // Info del usuario
        echo '<div class="user-info">';
        echo '<div class="user-avatar">';
        echo !empty($publicacion['Foto'])
            ? '<img src="data:image/jpeg;base64,' . base64_encode($publicacion['Foto']) . '" class="avatar-img">'
            : '<img src="../media/usuario.png" class="avatar-img">';
        echo '</div>';
        echo '<div class="user-details">';
        echo '<p>';
        echo '<span class="username">';
        echo '<a href="ver_perfil.php?id=' . $publicacion['usuarioID'] . '">';
        echo htmlspecialchars($publicacion['NombreC'] ?? $publicacion['Nick']);
        echo '</a>';
        echo '</span> ';
        echo '<span class="handle">';
        echo '<a href="ver_perfil.php?id=' . $publicacion['usuarioID'] . '">';
        echo '@' . htmlspecialchars($publicacion['Nick']);
        echo '</a>';
        echo '</span> · ';
        echo '<span class="time">' . date("H:i d/m/y", strtotime($publicacion['fechacreacion'])) . '</span>';
        echo '</p>';
        echo '</div></div>'; // fin user-info
        
        // Contenido de la publicación
        echo '<div class="post-content">';
        echo '<p>' . htmlspecialchars($publicacion['descripcion']) . '</p>';
        
        // Mostrar multimedia
        if (!empty($publicacion['multimedia'])) {
            echo '<div class="post-media">';
            foreach ($publicacion['multimedia'] as $media) {
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
        
        // Sección de comentarios
        echo '<div class="comments-section" id="comentarios-' . $publiID . '" style="display:none;">';
        
        if (!empty($publicacion['comentarios'])) {
            echo '<div class="comments-list">';
            foreach ($publicacion['comentarios'] as $coment) {
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
}
?>