<?php
session_start();
require_once 'conex.php';

header('Content-Type: application/json');

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$publiID = filter_input(INPUT_POST, 'publiID', FILTER_VALIDATE_INT);
if (!$publiID || $publiID < 1) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de publicación no válido']);
    exit();
}

$idUsuario = $_SESSION['id_usuario'];

// Verificar conexión a la base de datos
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit();
}

// Verificar que la publicación pertenezca al usuario
$sqlVerificar = "SELECT usuarioID FROM Publicaciones WHERE ID = ?";
$stmtVerificar = $conn->prepare($sqlVerificar);
if (!$stmtVerificar) {
    echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta']);
    exit();
}

$stmtVerificar->bind_param("i", $publiID);
$stmtVerificar->execute();
$resultVerificar = $stmtVerificar->get_result();

if ($resultVerificar->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Publicación no encontrada']);
    $stmtVerificar->close();
    $conn->close();
    exit();
}

$publicacion = $resultVerificar->fetch_assoc();
if ($publicacion['usuarioID'] != $idUsuario) {
    echo json_encode(['success' => false, 'message' => 'No tienes permiso para eliminar esta publicación']);
    $stmtVerificar->close();
    $conn->close();
    exit();
}

$stmtVerificar->close();

// Iniciar transacción
$conn->begin_transaction();

try {
    // Eliminar comentarios
    $stmt = $conn->prepare("DELETE FROM Comentarios WHERE publiID = ?");
    if (!$stmt) throw new Exception("Error al preparar eliminación de comentarios");
    $stmt->bind_param("i", $publiID);
    $stmt->execute();
    $stmt->close();

    // Eliminar reacciones
    $stmt = $conn->prepare("DELETE FROM Reacciones WHERE publiID = ?");
    if (!$stmt) throw new Exception("Error al preparar eliminación de reacciones");
    $stmt->bind_param("i", $publiID);
    $stmt->execute();
    $stmt->close();

    // Eliminar archivos multimedia
    $stmt = $conn->prepare("DELETE FROM Multimedia WHERE publiID = ?");
    if (!$stmt) throw new Exception("Error al preparar eliminación de multimedia");
    $stmt->bind_param("i", $publiID);
    $stmt->execute();
    $stmt->close();

    // Eliminar la publicación
    $stmt = $conn->prepare("DELETE FROM Publicaciones WHERE ID = ?");
    if (!$stmt) throw new Exception("Error al preparar eliminación de publicación");
    $stmt->bind_param("i", $publiID);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    error_log("Error al eliminar publicación: " . $e->getMessage()); // Se recomienda revisar los logs del servidor
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
}

$conn->close();
?>
