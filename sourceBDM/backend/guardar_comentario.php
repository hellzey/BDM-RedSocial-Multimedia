<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'conex.php';

$response = ['success' => false, 'message' => ''];

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    $response['message'] = 'Debes iniciar sesión para comentar';
    echo json_encode($response);
    exit;
}

// Verificar que se recibieron los datos necesarios
if (!isset($_POST['publiID']) || !isset($_POST['comentario']) || empty($_POST['comentario'])) {
    $response['message'] = 'Datos incompletos';
    echo json_encode($response);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$publiID = $_POST['publiID'];
$comentario = trim($_POST['comentario']);

// Preparar la consulta para insertar el comentario
$sql = "INSERT INTO Comentarios (publiID, usuarioID, comentario, fecha_comentario) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $publiID, $id_usuario, $comentario);

// Ejecutar la consulta
if ($stmt->execute()) {
    // Obtener el nick del usuario
    $sqlUser = "SELECT Nick FROM Usuarios WHERE ID = ?";
    $stmtUser = $conn->prepare($sqlUser);
    $stmtUser->bind_param("i", $id_usuario);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();
    $userNick = $resultUser->fetch_assoc()['Nick'];
    
    $response['success'] = true;
    $response['comentario'] = htmlspecialchars($comentario);
    $response['userNick'] = htmlspecialchars($userNick);
} else {
    $response['message'] = 'Error al guardar el comentario: ' . $conn->error;
}

// Devolver la respuesta como JSON
echo json_encode($response);
?>