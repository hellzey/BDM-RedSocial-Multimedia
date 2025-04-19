<?php
session_start();
require 'conex.php';

$emisor_id = $_SESSION['id_usuario'];
$grupo_id = intval($_POST['grupo']);
$mensaje = trim($_POST['mensaje']);

if (empty($mensaje)) {
    echo "El mensaje no puede estar vacío";
    exit;
}

// Verificar que el usuario sea miembro del grupo
$sql = "SELECT COUNT(*) as esMiembro FROM MiembrosGrupo WHERE grupoID = ? AND usuarioID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $grupo_id, $emisor_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['esMiembro'] < 1) {
    echo "No eres miembro de este grupo.";
    exit;
}

// Insertar el mensaje
$sql = "INSERT INTO MensajesGrupo (grupoID, emisorID, contenido) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $grupo_id, $emisor_id, $mensaje);

if ($stmt->execute()) {
    echo "ok";
} else {
    echo "Error al enviar el mensaje: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>