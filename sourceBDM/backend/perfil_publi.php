<?php
function obtenerPublicacionesUsuario($conn, $idUsuario) {
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

    return $publicaciones;
}
?>