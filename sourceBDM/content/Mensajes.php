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
    <link rel="Icon" href="../media/Freedom_Icono.png">
</head>
<body> 
    <?php include 'nav.php'; ?>
    
    <div class="mensaje-container">
        
        <div class="chat-list">
            <h3>Chats Recientes</h3>
            <button class="create-group" id="btnCrearGrupo">+ Crear Grupo</button>
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

    <!-- Modal para crear grupo -->
    <div id="grupoModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3>Crear nuevo grupo</h3>
            <input type="text" id="nombreGrupo" placeholder="Nombre del grupo" maxlength="50" required>
            <p>Selecciona hasta 2 contactos (solo puedes añadir personas con seguimiento mutuo):</p>
            <div class="user-selection" id="userSelection"></div>
            <div class="selected-count">Seleccionados: <span id="selectedCount">0</span>/2</div>
            <button id="btnConfirmarGrupo">Crear Grupo</button>
        </div>
    </div>

    <script>
        let receptorID = null;
        let esGrupo = false;
        let grupoID = null;
        const selectedUsers = new Set();
        const modal = document.getElementById("grupoModal");
        const btnCrearGrupo = document.getElementById("btnCrearGrupo");
        const closeBtn = document.querySelector(".close-btn");
        
        // Cargar chats (directos y grupos)
        function cargarUsuarios() {
            fetch('../backend/cargar_chats.php')
                .then(res => res.text())
                .then(html => {
                    document.getElementById('chat-usuarios').innerHTML = html;
                    // Agregar listeners a los chats directos
                    document.querySelectorAll('.chat-item').forEach(item => {
                        item.addEventListener('click', () => {
                            esGrupo = item.hasAttribute('data-grupo');
                            
                            if (esGrupo) {
                                grupoID = item.getAttribute('data-grupo');
                                receptorID = null;
                                document.getElementById('chat-titulo').innerText = "Grupo: " + item.innerText.replace(" (Grupo)", "");
                            } else {
                                receptorID = item.getAttribute('data-id');
                                grupoID = null;
                                document.getElementById('chat-titulo').innerText = "Chat con " + item.innerText;
                            }
                            
                            document.getElementById('chat-input').style.display = 'flex';
                            cargarMensajes();
                        });
                    });
                });
        }

        // Cargar mensajes (directos o de grupo)
        function cargarMensajes() {
            const formData = new FormData();
            
            if (esGrupo) {
                formData.append("grupo", grupoID);
                formData.append("tipo", "grupo");
                
                fetch('../backend/cargar_mensajes_grupo.php', {
                    method: "POST",
                    body: formData
                })
                .then(res => res.text())
                .then(html => {
                    const mensajesDiv = document.getElementById("mensajes");
                    mensajesDiv.innerHTML = html;
                    mensajesDiv.scrollTop = mensajesDiv.scrollHeight;
                });
            } else {
                formData.append("receptor", receptorID);
                formData.append("tipo", "directo");
                
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
        }

        // Enviar mensaje (directo o a grupo)
        function enviarMensaje() {
            const mensaje = document.getElementById("mensaje").value.trim();
            if (!mensaje) return;

            const formData = new FormData();
            formData.append("mensaje", mensaje);
            
            let url = '';
            
            if (esGrupo) {
                formData.append("grupo", grupoID);
                url = '../backend/enviar_mensaje_grupo.php';
            } else {
                formData.append("receptor", receptorID);
                url = '../backend/enviar_mensaje.php';
            }

            fetch(url, {
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

        // Modal para crear grupo
        btnCrearGrupo.onclick = function() {
            modal.style.display = "block";
            cargarContactosMutuos();
        }
        
        closeBtn.onclick = function() {
            modal.style.display = "none";
        }
        
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
        
        // Cargar contactos con seguimiento mutuo para el grupo
        function cargarContactosMutuos() {
            fetch('../backend/obtener_contactos_mutuos.php')
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById("userSelection");
                    container.innerHTML = "";
                    selectedUsers.clear();
                    updateSelectedCount();
                    
                    if (data.length === 0) {
                        container.innerHTML = "<p>No tienes contactos con seguimiento mutuo.</p>";
                        return;
                    }
                    
                    data.forEach(user => {
                        const div = document.createElement("div");
                        div.className = "user-option";
                        div.textContent = user.Nick;
                        div.dataset.id = user.ID;
                        
                        div.addEventListener("click", function() {
                            if (this.classList.contains("selected")) {
                                this.classList.remove("selected");
                                selectedUsers.delete(this.dataset.id);
                            } else {
                                if (selectedUsers.size < 2) {
                                    this.classList.add("selected");
                                    selectedUsers.add(this.dataset.id);
                                }
                            }
                            updateSelectedCount();
                        });
                        
                        container.appendChild(div);
                    });
                });
        }
        
        function updateSelectedCount() {
            document.getElementById("selectedCount").textContent = selectedUsers.size;
        }
        
        // Crear grupo
        document.getElementById("btnConfirmarGrupo").addEventListener("click", function() {
            const nombreGrupo = document.getElementById("nombreGrupo").value.trim();
            
            if (!nombreGrupo) {
                alert("Ingresa un nombre para el grupo");
                return;
            }
            
            if (selectedUsers.size === 0) {
                alert("Debes seleccionar al menos un contacto");
                return;
            }
            
            const formData = new FormData();
            formData.append("nombre", nombreGrupo);
            
            const miembros = Array.from(selectedUsers);
            formData.append("miembros", JSON.stringify(miembros));
            
            fetch('../backend/crear_grupo.php', {
                method: "POST",
                body: formData
            })
            .then(res => res.text())
            .then(resp => {
                if (resp.startsWith("ok:")) {
                    const grupoId = resp.split(":")[1];
                    alert("Grupo creado correctamente");
                    modal.style.display = "none";
                    cargarUsuarios();
                    
                    // Automáticamente abrir el grupo recién creado
                    setTimeout(() => {
                        esGrupo = true;
                        grupoID = grupoId;
                        receptorID = null;
                        document.getElementById('chat-titulo').innerText = "Grupo: " + nombreGrupo;
                        document.getElementById('chat-input').style.display = 'flex';
                        cargarMensajes();
                    }, 500);
                } else {
                    alert(resp);
                }
            });
        });

        document.getElementById("btnEnviar").addEventListener("click", enviarMensaje);
        document.getElementById("mensaje").addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                enviarMensaje();
            }
        });

        // Cargar chats al iniciar
        cargarUsuarios();
        
        // Actualizar periódicamente
        setInterval(function() {
            if (receptorID || grupoID) {
                cargarMensajes();
            }
        }, 5000);
    </script>
</body>
</html>