<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes</title>
    <link rel="stylesheet" href="../css/Mensajes.css">
</head>
<body> 
    <?php include 'nav.php'; ?>
    
    <div class="mensaje-container">
        
        <div class="chat-list">
            <h3>Chats Recientes</h3>
            <button class="create-group">+ Crear Grupo</button>
            <ul id="chat-usuarios"></ul>
        </div>

        <div class="chat-box">
            <h3 id="chat-titulo">Selecciona un chat</h3>
            <div class="messages" id="mensajes"></div>
            <div class="chat-input" style="display: none;" id="chat-input">
                <input type="text" id="mensaje" placeholder="Escribe un mensaje...">
                <button id="btnEnviar">Enviar</button>
            </div>
        </div>
    </div>

    <script>
        let receptorID = null;

        function cargarUsuarios() {
            fetch('../backend/cargar_chats.php')
                .then(res => res.text())
                .then(html => {
                    document.getElementById('chat-usuarios').innerHTML = html;
                    document.querySelectorAll('.chat-item').forEach(item => {
                        item.addEventListener('click', () => {
                            receptorID = item.getAttribute('data-id');
                            document.getElementById('chat-titulo').innerText = "Chat con " + item.innerText;
                            document.getElementById('chat-input').style.display = 'flex';
                            cargarMensajes();
                        });
                    });
                });
        }

        function cargarMensajes() {
            const formData = new FormData();
            formData.append("receptor", receptorID);

            fetch('../backend/cargar_mensajes.php', {
                method: "POST",
                body: formData
            })
            .then(res => res.text())
            .then(html => {
                const mensajesDiv = document.getElementById("mensajes");
                mensajesDiv.innerHTML = html;
                mensajesDiv.scrollTop = mensajesDiv.scrollHeight;
            });
        }

        function enviarMensaje() {
            const mensaje = document.getElementById("mensaje").value.trim();
            if (!mensaje) return;

            const formData = new FormData();
            formData.append("receptor", receptorID);
            formData.append("mensaje", mensaje);

            fetch('../backend/enviar_mensaje.php', {
                method: "POST",
                body: formData
            })
            .then(res => res.text())
            .then(resp => {
                if (resp === "ok") {
                    document.getElementById("mensaje").value = "";
                    cargarMensajes();
                } else {
                    alert(resp);
                }
            });
        }

        document.getElementById("btnEnviar").addEventListener("click", enviarMensaje);
        document.getElementById("mensaje").addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                enviarMensaje();
            }
        });

        cargarUsuarios();
    </script>
</body>
</html>
