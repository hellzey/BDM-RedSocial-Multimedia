<?php
include 'conex.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login/iniciosesion.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Elimina el usuario. Todo lo relacionado se borrará automáticamente por ON DELETE CASCADE
$stmt = $conn->prepare("DELETE FROM Usuarios WHERE ID = ?");
$stmt->bind_param("i", $id_usuario);

if ($stmt->execute()) {
    session_unset();
    session_destroy();
    header("Location: ../login/registro.php");
    exit;
} else {
    echo "❌ Error al eliminar el perfil.";
}
?>
