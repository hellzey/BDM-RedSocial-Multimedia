<?php
// Aseguramos que se inicie la sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluimos la conexión a la base de datos
include 'conex.php'; // Ajusta la ruta según tu estructura de archivos

$response = ['success' => false, 'message' => '', 'likes' => 0, 'liked' => false];

// Verificamos que haya una sesión activa
if (!isset($_SESSION['id_usuario'])) {
    $response['message'] = 'Debes iniciar sesión para dar me gusta';
    echo json_encode($response);
    exit;
}

// Verificamos que se recibió el ID de la publicación
if (!isset($_POST['publiID']) || empty($_POST['publiID'])) {
    $response['message'] = 'ID de publicación no proporcionado';
    echo json_encode($response);
    exit;
}

// Obtenemos y sanitizamos los datos
$id_usuario = (int)$_SESSION['id_usuario'];
$publiID = (int)$_POST['publiID'];

// Verificamos que la publicación exista
$sqlCheckPost = "SELECT publiID FROM Publicaciones WHERE publiID = ?";
$stmtCheckPost = $conn->prepare($sqlCheckPost);
$stmtCheckPost->bind_param("i", $publiID);
$stmtCheckPost->execute();
$resultPost = $stmtCheckPost->get_result();

if ($resultPost->num_rows === 0) {
    $response['message'] = 'La publicación no existe';
    echo json_encode($response);
    exit;
}

// Verificamos si el usuario ya dio me gusta
$sqlCheck = "SELECT reaccionID FROM Reacciones WHERE publiID = ? AND usuarioID = ? AND tipo = 1";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("ii", $publiID, $id_usuario);
$stmtCheck->execute();
$result = $stmtCheck->get_result();

if ($result->num_rows > 0) {
    // Si ya dio like, lo quitamos
    $sqlDelete = "DELETE FROM Reacciones WHERE publiID = ? AND usuarioID = ? AND tipo = 1";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("ii", $publiID, $id_usuario);
    
    if ($stmtDelete->execute()) {
        $response['success'] = true;
        $response['liked'] = false;
        $response['message'] = 'Me gusta removido exitosamente';
    } else {
        $response['message'] = 'Error al quitar me gusta: ' . $conn->error;
    }
} else {
    // Si no ha dado like, lo agregamos
    $sqlInsert = "INSERT INTO Reacciones (publiID, usuarioID, tipo, fecha) VALUES (?, ?, 1, NOW())";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("ii", $publiID, $id_usuario);
    
    if ($stmtInsert->execute()) {
        $response['success'] = true;
        $response['liked'] = true;
        $response['message'] = 'Me gusta agregado exitosamente';
    } else {
        $response['message'] = 'Error al dar me gusta: ' . $conn->error;
    }
}

// Si la operación fue exitosa, actualizamos el contador de likes
if ($response['success']) {
    $sqlCount = "SELECT COUNT(*) as total FROM Reacciones WHERE publiID = ? AND tipo = 1";
    $stmtCount = $conn->prepare($sqlCount);
    $stmtCount->bind_param("i", $publiID);
    $stmtCount->execute();
    $resultCount = $stmtCount->get_result();
    $row = $resultCount->fetch_assoc();
    $response['likes'] = $row['total'];
}

// Devolvemos la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
?>