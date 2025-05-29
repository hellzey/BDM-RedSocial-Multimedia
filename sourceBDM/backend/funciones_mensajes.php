<?php
session_start();
require 'conex.php';

$id = $_SESSION['id_usuario'];

$sql = "
    SELECT u.ID, u.Nick
    FROM VistaAmigos va
    JOIN Usuarios u ON (u.ID = va.Usuario1 OR u.ID = va.Usuario2)
    WHERE (? IN (va.Usuario1, va.Usuario2)) AND u.ID != ?
    ORDER BY u.Nick
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $id);
$stmt->execute();
$result = $stmt->get_result();

$amigos = [];
while ($row = $result->fetch_assoc()) {
    $amigos[] = [
        'ID' => $row['ID'],
        'Nick' => $row['Nick']
    ];
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($amigos);
?>
