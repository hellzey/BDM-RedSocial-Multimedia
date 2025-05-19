<?php
include '../backend/conex.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$resultados = [];

if ($q !== '') {
    if ($q[0] === '#') {
        // Búsqueda por categoría
        $categoria = substr($q, 1);
        $stmt = $conn->prepare("
            SELECT p.*, u.Nick, u.NombreC, u.Foto
            FROM Publicaciones p
            JOIN Publicaciones_Categorias pc ON p.publiID = pc.publiID
            JOIN Categorias c ON pc.categoriaID = c.categoriaID
            JOIN Usuarios u ON p.usuarioID = u.ID
            WHERE c.categoria LIKE ?
            ORDER BY p.fechacreacion DESC
        ");
        $like = "%" . $categoria . "%";
        $stmt->bind_param("s", $like);
    } else {
        // Búsqueda por usuario o contenido
        $stmt = $conn->prepare("
            SELECT p.*, u.Nick, u.NombreC, u.Foto
            FROM Publicaciones p
            JOIN Usuarios u ON p.usuarioID = u.ID
            WHERE u.Nick LIKE ? OR u.NombreC LIKE ? OR p.descripcion LIKE ?
            ORDER BY p.fechacreacion DESC
        ");
        $like = "%" . $q . "%";
        $stmt->bind_param("sss", $like, $like, $like);
    }

    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $resultados[] = $row;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados de búsqueda</title>
    <link rel="stylesheet" href="../css/ReBusqueda.css">
    <link rel="Icon" href="../media/Freedom_Icono.png">
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="container">
        <h2>Resultados de la búsqueda</h2>

        <div class="post-container">
            <?php if (empty($resultados)): ?>
                <p>No se encontraron resultados.</p>
            <?php else: ?>
                <?php foreach ($resultados as $post): ?>
    <div class="post">
        <div class="user-info">
            <div class="user-avatar">
                <img src="<?= $post['Foto'] ? 'data:image/jpeg;base64,' . base64_encode($post['Foto']) : '../media/usuario.png'; ?>" class="avatar-img">
            </div>
            <div class="user-details">
                <p>
                    <span class="username">
                        <a href="ver_perfil.php?id=<?= $post['usuarioID']; ?>">
                            <?= htmlspecialchars($post['NombreC']) ?>
                        </a>
                    </span>
                    <span class="handle">
                        <a href="ver_perfil.php?id=<?= $post['usuarioID']; ?>">
                            @<?= htmlspecialchars($post['Nick']) ?>
                        </a>
                    </span> ·
                    <span class="time"><?= date('H:i d/m/y', strtotime($post['fechacreacion'])) ?></span>
                </p>
            </div>
        </div>
        <p><?= nl2br(htmlspecialchars($post['descripcion'])) ?></p>
    </div>
<?php endforeach; ?>

            <?php endif; ?>
        </div>
    </div>
</body>
</html>
