<?php
session_start();
require_once '../backend/conex.php';
require_once '../backend/usuario_info.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: iniciosesion.php");
    exit();
}

$usuario_actual = $_SESSION['id_usuario'];

// Determinar de qué usuario mostrar los seguidores
$id_perfil = isset($_GET['id']) ? $_GET['id'] : $usuario_actual;

// Obtener información del usuario del perfil
$usuario_perfil = obtenerInfoUsuario($conn, $id_perfil);

if (!$usuario_perfil) {
    echo "Usuario no encontrado";
    exit();
}

$nombre_perfil = $usuario_perfil['NombreC'];
$nick_perfil = $usuario_perfil['Nick'];

// Obtener todos los seguidores del usuario
function obtenerSeguidores($conn, $id_usuario) {
    $sql = "SELECT u.ID, u.NombreC, u.Nick, u.Foto 
            FROM Usuarios u 
            INNER JOIN Seguidores s ON u.ID = s.SeguidorID 
            WHERE s.SeguidoID = ? 
            ORDER BY s.fecha_seguimiento DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $seguidores = [];
    while ($row = $resultado->fetch_assoc()) {
        // Procesar la foto
        $row['Foto'] = $row['Foto'] ? 'data:image/jpeg;base64,' . base64_encode($row['Foto']) : '../media/default.jpg';
        $seguidores[] = $row;
    }
    
    return $seguidores;
}

// Verificar si el usuario actual sigue a otro usuario
function verificarSeguimiento($conn, $seguidor, $seguido) {
    $sql = "SELECT * FROM Seguidores WHERE SeguidorID = ? AND SeguidoID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $seguidor, $seguido);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Obtener los seguidores
$seguidores = obtenerSeguidores($conn, $id_perfil);

// Procesar acción de seguir/dejar de seguir
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion_seguir']) && isset($_POST['usuario_id'])) {
    $usuario_objetivo = $_POST['usuario_id'];
    
    // Verificar que no intente seguirse a sí mismo
    if ($usuario_objetivo != $usuario_actual) {
        if ($_POST['accion_seguir'] == 'seguir') {
            // Seguir al usuario
            $sql = "INSERT INTO Seguidores (SeguidorID, SeguidoID) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $usuario_actual, $usuario_objetivo);
            $stmt->execute();
            
            // Actualizar contador de seguidores
            $sql = "UPDATE Usuarios SET N_seguidores = N_seguidores + 1 WHERE ID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $usuario_objetivo);
            $stmt->execute();
        } elseif ($_POST['accion_seguir'] == 'dejar_seguir') {
            // Dejar de seguir al usuario
            $sql = "DELETE FROM Seguidores WHERE SeguidorID = ? AND SeguidoID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $usuario_actual, $usuario_objetivo);
            $stmt->execute();
            
            // Actualizar contador de seguidores
            $sql = "UPDATE Usuarios SET N_seguidores = N_seguidores - 1 WHERE ID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $usuario_objetivo);
            $stmt->execute();
        }
    }
    
    // Redireccionar para evitar reenvío de formulario
    header("Location: followers.php" . (isset($_GET['id']) ? "?id=" . $_GET['id'] : ""));
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguidores de <?php echo htmlspecialchars($nombre_perfil); ?></title>
    <link rel="stylesheet" href="../css/followers.css">
    <link rel="Icon" href="../media/Freedom_Icono.png">
</head>
<body> 
    <?php include 'nav.php'; ?>

    <div class="container">
        <div class="follow-container">
            <h3>
                <?php if ($id_perfil == $usuario_actual): ?>
                    Tus seguidores
                <?php else: ?>
                    Seguidores de <?php echo htmlspecialchars($nombre_perfil); ?> (@<?php echo htmlspecialchars($nick_perfil); ?>)
                <?php endif; ?>
            </h3>
            
            <div class="back-link">
                <a href="<?php echo ($id_perfil == $usuario_actual) ? 'perfil.php' : 'ver_perfil.php?id=' . $id_perfil; ?>">
                    Volver al perfil
                </a>
            </div>
            
            <ul class="followers-list">
                <?php if (empty($seguidores)): ?>
                    <li class="no-followers">
                        <?php if ($id_perfil == $usuario_actual): ?>
                            Aún no tienes seguidores.
                        <?php else: ?>
                            Este usuario aún no tiene seguidores.
                        <?php endif; ?>
                    </li>
                <?php else: ?>
                    <?php foreach ($seguidores as $seguidor): ?>
                        <li class="follower">
                            <a href="ver_perfil.php?id=<?php echo $seguidor['ID']; ?>" class="follower-avatar">
                                <img src="<?php echo $seguidor['Foto']; ?>" alt="<?php echo htmlspecialchars($seguidor['NombreC']); ?>">
                            </a>
                            <div class="follower-info">
                                <a href="ver_perfil.php?id=<?php echo $seguidor['ID']; ?>" class="follower-link">
                                    <p class="follower-name"><?php echo htmlspecialchars($seguidor['NombreC']); ?></p>
                                    <p class="follower-handle">@<?php echo htmlspecialchars($seguidor['Nick']); ?></p>
                                </a>
                            </div>
                            
                            <?php if ($seguidor['ID'] != $usuario_actual): ?>
                                <?php $ya_sigue = verificarSeguimiento($conn, $usuario_actual, $seguidor['ID']); ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="usuario_id" value="<?php echo $seguidor['ID']; ?>">
                                    <?php if ($ya_sigue): ?>
                                        <input type="hidden" name="accion_seguir" value="dejar_seguir">
                                        <button type="submit" class="follow-btn following">Siguiendo</button>
                                    <?php else: ?>
                                        <input type="hidden" name="accion_seguir" value="seguir">
                                        <button type="submit" class="follow-btn">Seguir</button>
                                    <?php endif; ?>
                                </form>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>

            <!-- Generar reporte de seguidores --> 
            <div class="follow-container">
            <h3>Reporte de mis seguidores</h3>
            <form action="../backend/reporte_seguidores.php" method="GET" style="display:inline;">
            <input type="hidden" name="usuarioID" value="<?php echo $id_perfil; ?>">
            <button type="submit" class="reporte-btn" name="formato" value="csv">Descargar CSV</button>
            </form>
            <form action="../backend/reporte_seguidores.php" method="GET" style="display:inline;">
            <input type="hidden" name="usuarioID" value="<?php echo $id_perfil; ?>">
            <button type="submit" class="reporte-btn" name="formato" value="pdf">Descargar PDF</button>
            </form>
            </div>

        </div> 
    </div>

    <script>
        // Cambiar texto del botón "Siguiendo" al pasar el mouse
        document.querySelectorAll('.follow-btn.following').forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.textContent = 'Dejar de seguir';
                this.classList.add('unfollow-hover');
            });
            
            button.addEventListener('mouseleave', function() {
                this.textContent = 'Siguiendo';
                this.classList.remove('unfollow-hover');
            });
        });
    </script>
</body>
</html>