<?php
function procesarPublicacion($conn, $descripcion, $idUsuario, $archivos) {
    // Detectar categorías (hashtags)
    preg_match_all('/#(\w+)/', $descripcion, $matches);
    $categorias = $matches[1];

    // Iniciar transacción
    $conn->begin_transaction();
    
    try {
        // Insertar la publicación
        $sql_pub = "INSERT INTO Publicaciones (descripcion, usuarioID, estatus) VALUES (?, ?, 1)";
        $stmt_pub = $conn->prepare($sql_pub);
        $stmt_pub->bind_param("si", $descripcion, $idUsuario);
        $stmt_pub->execute();
        $publiID = $stmt_pub->insert_id;

        // Insertar las categorías
        procesarCategorias($conn, $categorias, $publiID);

        // Procesar los archivos multimedia
        if (!empty($archivos['name'][0])) {
            procesarMultimedia($conn, $archivos, $publiID);
        }

        // Confirmar transacción
        $conn->commit();
        return true;
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollback();
        return false;
    }
}

function procesarCategorias($conn, $categorias, $publiID) {
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
}

function procesarMultimedia($conn, $archivos, $publiID) {
    foreach ($archivos['tmp_name'] as $key => $tmp_name) {
        if (empty($tmp_name)) continue;
        
        $file_tmp = $archivos['tmp_name'][$key];
        $file_type = $archivos['type'][$key];

        if (strpos($file_type, 'image') !== false) {
            $tipo = 'imagen';
        } elseif (strpos($file_type, 'video') !== false) {
            $tipo = 'video';
        } else {
            continue; // Ignorar tipos de archivo no soportados
        }

        $file_data = file_get_contents($file_tmp);

        $sql_media = "INSERT INTO MultimediaPublicaciones (publiID, tipo, archivo) VALUES (?, ?, ?)";
        $stmt_media = $conn->prepare($sql_media);
        $stmt_media->bind_param("iss", $publiID, $tipo, $file_data);
        $stmt_media->execute();
    }
}
?>