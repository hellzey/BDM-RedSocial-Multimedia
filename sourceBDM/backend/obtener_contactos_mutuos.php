<?php
session_start();
require 'conex.php';

$id = $_SESSION['id_usuario'];

// Obtener usuarios con seguimiento mutuo, incluyendo su número de seguidores
$sql = "
    SELECT u.ID, u.Nick, u.N_seguidores
    FROM Usuarios u
    INNER JOIN Seguidores s1 ON s1.SeguidoID = u.ID AND s1.SeguidorID = ?
    INNER JOIN Seguidores s2 ON s2.SeguidorID = u.ID AND s2.SeguidoID = ?
    ORDER BY u.Nick
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $id);
$stmt->execute();
$result = $stmt->get_result();

$usuarios = array();
while ($row = $result->fetch_assoc()) {
    $usuarios[] = array(
        'ID' => $row['ID'],
        'Nick' => $row['Nick'],
        'N_seguidores' => $row['N_seguidores'] // Aquí incluyes el número de seguidores
    );
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($usuarios);
?>
