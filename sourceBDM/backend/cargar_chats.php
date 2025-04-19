<?php
session_start();
require 'conex.php';

$id = $_SESSION['id_usuario'];

// Primero cargar chats directos (usuarios que se siguen mutuamente)
$sql = "
    SELECT u.ID, u.Nick
    FROM Usuarios u
    INNER JOIN Seguidores s1 ON s1.SeguidoID = u.ID AND s1.SeguidorID = ?
    INNER JOIN Seguidores s2 ON s2.SeguidorID = u.ID AND s2.SeguidoID = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<li class='chat-item' data-id='" . $row['ID'] . "'>" . htmlspecialchars($row['Nick']) . "</li>";
}

$stmt->close();

// Luego cargar grupos a los que pertenece el usuario
$sql = "
    SELECT g.grupoID, g.nombre
    FROM GruposChat g
    INNER JOIN MiembrosGrupo m ON g.grupoID = m.grupoID
    WHERE m.usuarioID = ?
    ORDER BY g.fecha_creacion DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<li class='chat-item' data-grupo='" . $row['grupoID'] . "'>" . 
         htmlspecialchars($row['nombre']) . " <span class='group-indicator'>Grupo</span></li>";
}

$stmt->close();
$conn->close();
?>