<?php
session_start();
require 'conex.php';

$usuario_id = $_SESSION['id_usuario'];
$grupo_id = intval($_POST['grupo']);

// Verificar que el usuario sea miembro del grupo
$sql = "SELECT COUNT(*) as esMiembro FROM MiembrosGrupo WHERE grupoID = ? AND usuarioID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $grupo_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['esMiembro'] < 1) {
    echo "<p class='error-message'>No eres miembro de este grupo.</p>";
    exit;
}

// Obtener los mensajes del grupo
$sql = "SELECT m.*, u.Nick 
        FROM MensajesGrupo m
        JOIN Usuarios u ON m.emisorID = u.ID
        WHERE m.grupoID = ?
        ORDER BY m.fecha_envio ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $grupo_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $clase = $row['emisorID'] == $usuario_id ? 'sent' : 'received';
    $nombre = $row['emisorID'] == $usuario_id ? 'Tú' : htmlspecialchars($row['Nick']);
    $hora = date("h:i A", strtotime($row['fecha_envio']));
    echo "<p class='message $clase'>
            <span class='user'>$nombre</span> 
            <span class='timestamp'>$hora</span><br>
            " . htmlspecialchars($row['contenido']) . "
          </p>";
}

$stmt->close();
$conn->close();
?>