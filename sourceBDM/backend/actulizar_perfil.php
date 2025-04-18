<?php
include 'conex.php';
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login/iniciosesion.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$nick = $_POST['nick'];
$biografia = $_POST['biografia'];
$foto = null;

// Procesar imagen si fue subida
if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
    $foto = file_get_contents($_FILES['foto_perfil']['tmp_name']);
}

$sql = $foto 
    ? "UPDATE usuarios SET Nick = ?, Biografia = ?, Foto = ? WHERE ID = ?"
    : "UPDATE usuarios SET Nick = ?, Biografia = ? WHERE ID = ?";

$stmt = $conn->prepare($sql);

if ($foto) {
    $stmt->bind_param("sssi", $nick, $biografia, $foto, $id_usuario);
} else {
    $stmt->bind_param("ssi", $nick, $biografia, $id_usuario);
}

if ($stmt->execute()) {
    header("Location: ../content/perfil.php"); // redirige al perfil actualizado
} else {
    echo "Error al actualizar: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
