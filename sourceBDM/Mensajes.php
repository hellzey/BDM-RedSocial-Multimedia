<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes</title>
    <link rel="stylesheet" href="css/Mensajes.css">
</head>
<body> 
    <?php include 'nav.php'; ?>
    
    <div class="mensaje-container">
        <!-- Panel de chats recientes -->
        <div class="chat-list">
            <h3>Chats Recientes</h3>
            <ul>
                <li class="chat-item">Usuario 1</li>
                <li class="chat-item">Usuario 2</li>
                <li class="chat-item">Usuario 3</li>
                <li class="chat-item">Usuario 4</li>
            </ul>
        </div>

        <!-- Panel del chat -->
        <div class="chat-box">
            <h3>Chat con Usuario 1</h3>
            <div class="messages">
                <p class="message received">Hola, ¿cómo estás?</p>
                <p class="message sent">¡Bien! ¿Y tú?</p>
                <p class="message received">Todo bien, gracias.</p>
            </div>
            <div class="chat-input">
                <input type="text" placeholder="Escribe un mensaje...">
                <button>Enviar</button>
            </div>
        </div>
    </div>

</body>
</html>
