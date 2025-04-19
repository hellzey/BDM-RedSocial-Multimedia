<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'conex.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$publiID = isset($_POST['publiID']) ? intval($_POST['publiID']) : 0;

if ($publiID <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de publicación inválido']);
    exit;
}

// Verificar si ya existe un like
$sqlCheck = "SELECT reaccionID FROM Reacciones WHERE usuarioID = ? AND publiID = ? AND tipo = 1";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("ii", $id_usuario, $publiID);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows > 0) {
    // Ya existe el like, eliminarlo
    $row = $resultCheck->fetch_assoc();
    $reaccionID = $row['reaccionID'];
    
    $sqlDelete = "DELETE FROM Reacciones WHERE reaccionID = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $reaccionID);
    
    if ($stmtDelete->execute()) {
        $liked = false;
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al quitar like']);
        exit;
    }
} else {
    // No existe like, añadirlo
    $tipo = 1; // 1 = Me gusta
    $sqlInsert = "INSERT INTO Reacciones (usuarioID, publiID, tipo) VALUES (?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("iii", $id_usuario, $publiID, $tipo);
    
    if ($stmtInsert->execute()) {
        $liked = true;
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al añadir like']);
        exit;
    }
}

// Contar likes actuales
$sqlCount = "SELECT COUNT(*) as total FROM Reacciones WHERE publiID = ? AND tipo = 1";
$stmtCount = $conn->prepare($sqlCount);
$stmtCount->bind_param("i", $publiID);
$stmtCount->execute();
$resultCount = $stmtCount->get_result();
$likeCount = $resultCount->fetch_assoc()['total'];

echo json_encode([
    'success' => true,
    'liked' => $liked,
    'count' => $likeCount
]);
?>