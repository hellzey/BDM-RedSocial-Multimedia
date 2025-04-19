<?php
session_start();
require 'conex.php';

$id = $_SESSION['id_usuario'];

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
$conn->close();
?>
