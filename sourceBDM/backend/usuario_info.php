<?php
function obtenerInfoUsuario($conn, $idUsuario) {
    $sql = "SELECT NombreC, Nick, Genero, Email, Fecha_Nac, Foto, N_seguidores, Biografia FROM Usuarios WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        
        // Procesar la foto del usuario
        $usuario['Foto'] = $usuario['Foto'] ? 'data:image/jpeg;base64,' . base64_encode($usuario['Foto']) : '../media/default.jpg';
        
        return $usuario;
    } else {
        return false;
    }
}
?>