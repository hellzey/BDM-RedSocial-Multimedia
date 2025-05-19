<?php
session_start();
require 'conex.php';

if (!isset($_SESSION['id_usuario'])) {
    header("HTTP/1.1 401 Unauthorized");
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$idUsuario = $_SESSION['id_usuario'];
$publiID = $_POST['publiID'] ?? null;
$descripcion = $_POST['descripcion'] ?? '';

if (!$publiID) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(['success' => false, 'message' => 'ID de publicación no proporcionado']);
    exit();
}

// Verificar propiedad de la publicación
$sqlVerificar = "SELECT usuarioID FROM Publicaciones WHERE ID = ?";
$stmtVerificar = $conn->prepare($sqlVerificar);
$stmtVerificar->bind_param("i", $publiID);
$stmtVerificar->execute();
$resultVerificar = $stmtVerificar->get_result();

if ($resultVerificar->num_rows === 0) {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(['success' => false, 'message' => 'Publicación no encontrada']);
    exit();
}

$publicacion = $resultVerificar->fetch_assoc();
if ($publicacion['usuarioID'] != $idUsuario) {
    header("HTTP/1.1 403 Forbidden");
    echo json_encode(['success' => false, 'message' => 'No tienes permiso para editar esta publicación']);
    exit();
}

// Iniciar transacción
$conn->begin_transaction();

try {
    // 1. Actualizar descripción
    $sqlActualizar = "UPDATE Publicaciones SET descripcion = ? WHERE ID = ?";
    $stmtActualizar = $conn->prepare($sqlActualizar);
    $stmtActualizar->bind_param("si", $descripcion, $publiID);
    $stmtActualizar->execute();

    // 2. Procesar eliminación de medios
    if (isset($_POST['eliminar_media']) && is_array($_POST['eliminar_media'])) {
        foreach ($_POST['eliminar_media'] as $mediaID) {
            $mediaID = intval($mediaID);
            $sqlEliminarMedia = "DELETE FROM Multimedia WHERE ID = ? AND publiID = ?";
            $stmtEliminarMedia = $conn->prepare($sqlEliminarMedia);
            $stmtEliminarMedia->bind_param("ii", $mediaID, $publiID);
            $stmtEliminarMedia->execute();
        }
    }

    // 3. Procesar nuevos archivos
    if (!empty($_FILES['nuevos_archivos']['name'][0])) {
        foreach ($_FILES['nuevos_archivos']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['nuevos_archivos']['error'][$key] === UPLOAD_ERR_OK) {
                $nombreArchivo = $_FILES['nuevos_archivos']['name'][$key];
                $tipoArchivo = $_FILES['nuevos_archivos']['type'][$key];
                $archivoBinario = file_get_contents($tmp_name);
                $tipo = strpos($tipoArchivo, 'image') !== false ? 'imagen' : 'video';
                
                $sqlInsertarMedia = "INSERT INTO Multimedia (publiID, archivo, tipo, nombre_archivo) VALUES (?, ?, ?, ?)";
                $stmtInsertarMedia = $conn->prepare($sqlInsertarMedia);
                $stmtInsertarMedia->bind_param("isss", $publiID, $archivoBinario, $tipo, $nombreArchivo);
                $stmtInsertarMedia->execute();
            }
        }
    }

    $conn->commit();
    header("Location: ../perfil.php");
    exit();
} catch (Exception $e) {
    $conn->rollback();
    header("HTTP/1.1 500 Internal Server Error");
    echo "Error al actualizar la publicación: " . $e->getMessage();
    exit();
}
?>