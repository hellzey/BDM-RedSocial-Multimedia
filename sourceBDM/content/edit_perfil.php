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

$usuario = "MiUsuario";
$email = "usuario@example.com";
$fecha_nacimiento = "2000-01-01";
$imagen_perfil = "media/griffith.jpg"; 
?>

<div class="profile-container">
    <h2>Editar Perfil</h2>
    <form action="actualizar_perfil.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="usuario" value="<?php echo $usuario; ?>" required>
        <input type="email" name="email" value="<?php echo $email; ?>" required>
        <input type="date" name="fecha" value="<?php echo $fecha_nacimiento; ?>" required>
        
        
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
