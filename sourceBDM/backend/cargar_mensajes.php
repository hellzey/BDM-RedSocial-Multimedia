<?php
session_start();
require 'conex.php';

$emisor = $_SESSION['id_usuario'];
$receptor = intval($_POST['receptor']);

$sql = "SELECT * FROM Mensajes 
        WHERE (emisorID = ? AND receptorID = ?) 
           OR (emisorID = ? AND receptorID = ?)
        ORDER BY fecha_envio ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $emisor, $receptor, $receptor, $emisor);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $clase = $row['emisorID'] == $emisor ? 'sent' : 'received';
    $nombre = $row['emisorID'] == $emisor ? 'Tú' : 'Ellx';
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
