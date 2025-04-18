<?php
session_start();
require_once '../backend/conex.php'; // Asegúrate que este archivo contiene tu conexión $conn

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = trim($_POST['email']); // Puede ser correo o nombre de usuario
    $password = trim($_POST['password']);

    // Buscar por Nick o Email
    $query = "SELECT * FROM Usuarios WHERE Nick = ? OR Email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $input, $input);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // Verificar la contraseña
        if (password_verify($password, $usuario['Contra'])) {
            // Autenticación exitosa
            $_SESSION['id_usuario'] = $usuario['ID'];
            $_SESSION['nick'] = $usuario['Nick'];
            $_SESSION['admin'] = $usuario['Admin'];

            // Redirige al área protegida (puedes cambiar esto)
            header("Location: ../content/perfil.php");
            exit();
        } else {
            echo "<script>alert('Contraseña incorrecta'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Usuario no encontrado'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: iniciosesion.php");
    exit();
}
?>
