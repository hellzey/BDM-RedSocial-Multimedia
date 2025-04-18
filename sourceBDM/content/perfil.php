<?php
session_start();
require_once '../backend/conex.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: iniciosesion.php");
    exit();
}

$idUsuario = $_SESSION['id_usuario'];

$sql = "SELECT NombreC, Nick, Genero, Email, Fecha_Nac, Foto, N_seguidores, Biografia FROM Usuarios WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();

    $nombre = $usuario['NombreC'];
    $nick = $usuario['Nick'];
    $seguidores = $usuario['N_seguidores'];
    $biografia = $usuario['Biografia'];
    $foto = $usuario['Foto'] ? 'data:image/jpeg;base64,' . base64_encode($usuario['Foto']) : '../media/default.jpg';
} else {
    echo "Usuario no encontrado";
    exit();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="../css/perfil.css">
</head>
<body>
<?php include 'nav.php'; ?>
<div class="profile-container">
    <div class="profile-header">
        <img src="<?php echo $foto; ?>" alt="Foto de perfil" class="profile-img">
        <div class="profile-details">
            <p class="profile-name"><?php echo htmlspecialchars($nombre); ?></p>
            <p class="profile-handle">@<?php echo htmlspecialchars($nick); ?></p>
        </div>
    </div>

    <p class="profile-bio"><?php echo htmlspecialchars($biografia ?: "Este usuario aún no ha escrito su biografía."); ?></p>


    <p class="followers-count">
        <a href="followers.php"><?php echo $seguidores; ?> Seguidores</a><br>
        <a href="edit_perfil.php">Editar perfil</a>
    </p>
</div>

<div class="new-post-container">
    <h3>Hacer una nueva publicación</h3>
    <form>
        <textarea placeholder="¿Qué estás pensando?" rows="4"></textarea>
        <input type="file" id="file-upload" accept="image/*,video/*">
        <label for="file-upload" class="file-upload-label">archivo</label>
        <button type="submit">Publicar</button>
    </form>
</div>

<h2>Publicaciones del Usuario</h2>
<div class="post-container">
    <!-- Aquí se cargarán publicaciones dinámicamente en el futuro -->
</div>

<footer></footer>
</body>
</html>
