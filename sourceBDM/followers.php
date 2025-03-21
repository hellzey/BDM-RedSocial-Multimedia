<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguidores</title>
    <link rel="stylesheet" href="css/followers.css">
</head>
<body> 
    <?php include 'nav.php';  ?>

    <div class="container">
        <div class="follow-container">
            <h3>Seguidores</h3>
            <ul class="followers-list">
                <li class="follower">
                    <img src="media/usuario.png" alt="Usuario 1">
                    <div class="follower-info">
                        <p class="follower-name">Usuario 1</p>
                        <p class="follower-handle">@usuario1</p>
                    </div>
                    <button class="follow-btn">Siguiendo</button>
                </li>

               

                <li class="follower">
                    <img src="media/usuario.png" alt="Usuario 3">
                    <div class="follower-info">
                        <p class="follower-name">Usuario 3</p>
                        <p class="follower-handle">@usuario3</p>
                    </div>
                    <button class="follow-btn">Siguiendo</button>
                </li>
            </ul>
        </div> 
    </div>

</body>
</html>
