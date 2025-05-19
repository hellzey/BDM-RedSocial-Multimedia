<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>


<link rel="stylesheet" href="../css/nav.css">
<link rel="Icon" href="../media/Freedom_Icono.png">
<nav>
    <a href="inicio.php">
        <img src="../media/Freedom_Icono.png" alt="Logo" class="logo">
    </a>
    <a href="inicio.php">Inicio</a>

    <?php if (isset($_SESSION['id_usuario'])): ?>
        <a href="perfil.php">Perfil</a>
        <a href="Mensajes.php">Mensajes</a>
        <a href="../backend/cerrar_sesion.php">Salir</a>

        <form action="ReBusqueda.php" method="get" class="search-form">
            <input type="text" name="q" placeholder="Buscar..." class="search-input">
            <button type="submit" class="search-btn">Buscar</button>
        </form>
    <?php else: ?>
        <a href="../login/iniciosesion.php">Iniciar sesión</a>
        <a href="../login/registro.php">Registrarse</a>
    <?php endif; ?>
</nav>
