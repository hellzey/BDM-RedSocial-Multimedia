<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="../css/edit_perfil.css">
</head>
<body>

<?php include 'nav.php'; ?>
<?php
include '../backend/conex.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login/iniciosesion.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener los datos actuales del usuario
$sql = "SELECT Nick, Biografia, Foto FROM Usuarios WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();
    $nick = $usuario['Nick'];
    $biografia = $usuario['Biografia'];
    
    // Procesar la imagen (BLOB)
    if (!empty($usuario['Foto'])) {
        $imagen_perfil = 'data:image/jpeg;base64,' . base64_encode($usuario['Foto']);
    } else {
        $imagen_perfil = "../media/default.jpg"; // Ruta a imagen por defecto
    }
} else {
    echo "<p>Error al cargar datos del usuario.</p>";
    exit;
}
?>

<div class="profile-container">
    <h2>Editar Perfil</h2>
    <form action="../backend/actulizar_perfil.php" method="POST" enctype="multipart/form-data">
        <label>Nick:</label>
        <input type="text" name="nick" value="<?php echo htmlspecialchars($nick); ?>" required>

        <label>Biografía:</label>
        <textarea name="biografia" rows="4"><?php echo htmlspecialchars($biografia); ?></textarea>

        <div class="profile-pic-container">
            <img id="profile-pic" src="<?php echo $imagen_perfil; ?>" alt="Foto de perfil">
        </div>

        <label for="file-upload" class="file-upload-label">Cambiar Foto de Perfil</label>
        <input type="file" id="file-upload" name="foto_perfil" accept="image/*" style="display: none;">

        <button type="submit">Actualizar</button>
    </form>
</div>

<script>
document.getElementById("file-upload").addEventListener("change", function(event) {
    let file = event.target.files[0];
    if (file) {
        let reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById("profile-pic").src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>

</body>
</html>
