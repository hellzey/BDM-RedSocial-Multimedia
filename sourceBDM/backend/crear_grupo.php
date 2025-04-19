<?php
session_start();
require 'conex.php';
require 'funciones_mensajes.php';

$id_creador = $_SESSION['id_usuario'];
$nombre_grupo = trim($_POST['nombre']);
$miembros_json = $_POST['miembros'];

// Validaciones
if (empty($nombre_grupo)) {
    echo "El nombre del grupo no puede estar vacío";
    exit;
}

// Decodificar los IDs de los miembros
$miembros = json_decode($miembros_json);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Error al procesar los miembros del grupo";
    exit;
}

if (count($miembros) > 2) {
    echo "Solo puedes agregar hasta 2 miembros adicionales al grupo";
    exit;
}

// Verificar que todos los miembros tengan seguimiento mutuo con el creador
$todos_son_amigos = true;
foreach ($miembros as $miembro_id) {
    if (!sonAmigos($conn, $id_creador, $miembro_id)) {
        $todos_son_amigos = false;
        break;
    }
}

if (!$todos_son_amigos) {
    echo "Solo puedes crear grupos con usuarios que tengan seguimiento mutuo contigo";
    exit;
}

// Crear el grupo
$conn->begin_transaction();

try {
    // Insertar el grupo
    $sql = "INSERT INTO GruposChat (nombre, creadorID) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nombre_grupo, $id_creador);
    $stmt->execute();
    $grupo_id = $conn->insert_id;
    $stmt->close();
    
    // Agregar al creador como miembro
    $sql = "INSERT INTO MiembrosGrupo (grupoID, usuarioID) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $grupo_id, $id_creador);
    $stmt->execute();
    $stmt->close();
    
    // Agregar los demás miembros
    $sql = "INSERT INTO MiembrosGrupo (grupoID, usuarioID) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    
    foreach ($miembros as $miembro_id) {
        $stmt->bind_param("ii", $grupo_id, $miembro_id);
        $stmt->execute();
    }
    
    $stmt->close();
    $conn->commit();
    
    echo "ok:" . $grupo_id;
} catch (Exception $e) {
    $conn->rollback();
    echo "Error al crear el grupo: " . $e->getMessage();
}

$conn->close();
?>