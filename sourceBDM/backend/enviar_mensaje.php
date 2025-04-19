<?php
session_start();
require 'conex.php';

$emisor = $_SESSION['id_usuario'];
$receptor = intval($_POST['receptor']);
$mensaje = trim($_POST['mensaje']);

// Verificar si ambos usuarios se siguen mutuamente
$sql = "SELECT COUNT(*) AS total
        FROM Seguidores s1
        INNER JOIN Seguidores s2 
        ON s1.SeguidoID = s2.SeguidorID AND s1.SeguidorID = s2.SeguidoID
        WHERE s1.SeguidorID = ? AND s1.SeguidoID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $emisor, $receptor);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if ($res['total'] < 1) {
    echo "Solo puedes enviar mensajes a usuarios que te siguen y tú sigues.";
    exit;
}

// Insertar mensaje
$sql = "INSERT INTO Mensajes (emisorID, receptorID, contenido) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $emisor, $receptor, $mensaje);
if ($stmt->execute()) {
    echo "ok";
} else {
    echo "Error al enviar el mensaje.";
}

$stmt->close();
$conn->close();
?>
