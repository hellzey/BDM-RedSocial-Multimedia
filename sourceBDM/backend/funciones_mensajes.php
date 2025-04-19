<?php
function sonAmigos($conn, $user1, $user2) {
    $sql = "
        SELECT 1 
        FROM Seguidores s1
        JOIN Seguidores s2 
          ON s1.SeguidorID = s2.SeguidoID AND s1.SeguidoID = s2.SeguidorID
        WHERE s1.SeguidorID = ? AND s1.SeguidoID = ?
        LIMIT 1
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user1, $user2);
    $stmt->execute();
    $result = $stmt->get_result();
    $esAmigo = $result->num_rows > 0;
    $stmt->close();
    return $esAmigo;
}
?>
<?php