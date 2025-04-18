<?php
include("../backend/conex.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'] ?? '';
    $fechaNacimiento = $_POST['fechaNacimiento'] ?? '';
    $nombreUsuario = $_POST['nombreUsuario'] ?? '';
    $password = $_POST['password'] ?? '';
    $email = $_POST['email'] ?? '';
    $genero = $_POST['genero'] ?? '';

    // Cifrar contraseña
    $passwordCifrada = password_hash($password, PASSWORD_DEFAULT);

    // Leer la foto si se cargó
    $foto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto = file_get_contents($_FILES['foto']['tmp_name']);
    }

    // Preparar consulta
    $stmt = $conn->prepare("INSERT INTO Usuarios 
        (NombreC, Nick, Genero, Admin, Estatus, N_intentos, Fecha_Nac, Foto, Email, Contra)
        VALUES (?, ?, ?, 0, 1, 0, ?, ?, ?, ?)");

    // Vincular parámetros binarios por separado
    $stmt->send_long_data(3, $foto); // posición 3 corresponde al campo Foto

    $stmt->bind_param("sssssss", 
        $nombre, 
        $nombreUsuario, 
        $genero, 
        $fechaNacimiento, 
        $foto, 
        $email, 
        $passwordCifrada
    );

    if ($stmt->execute()) {
        echo "<script>alert('Registro exitoso'); window.location.href='iniciosesion.php';</script>";
    } else {
        echo "Error al registrar: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Acceso denegado.";
}
?>
