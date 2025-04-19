// Función para mostrar u ocultar comentarios
function mostrarComentarios(publiID) {
    const comentariosSection = document.getElementById('comentarios-' + publiID);
    if (comentariosSection.style.display === 'none') {
        comentariosSection.style.display = 'block';
    } else {
        comentariosSection.style.display = 'none';
    }
}

// Función para enviar un comentario
function enviarComentario(event, publiID) {
    event.preventDefault();
    
    const form = event.target;
    const comentarioText = form.comentario.value;
    
    // Crear FormData para enviar
    const formData = new FormData();
    formData.append('publiID', publiID);
    formData.append('comentario', comentarioText);
    
    // Enviar con fetch
    fetch('../backend/guardar_comentario.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar la sección de comentarios
            const comentariosSection = document.getElementById('comentarios-' + publiID);
            const commentsList = comentariosSection.querySelector('.comments-list') || document.createElement('div');
            
            if (!comentariosSection.querySelector('.comments-list')) {
                commentsList.className = 'comments-list';
                // Reemplazar el mensaje de "no hay comentarios"
                const noComments = comentariosSection.querySelector('.no-comments');
                if (noComments) {
                    comentariosSection.removeChild(noComments);
                }
                comentariosSection.insertBefore(commentsList, comentariosSection.querySelector('.comment-form'));
            }
            
            // Crear nuevo comentario
            const newComment = document.createElement('div');
            newComment.className = 'comment';
            newComment.innerHTML = `
                <span class="comment-username">@${data.userNick}:</span>
                <span class="comment-text">${data.comentario}</span>
            `;
            
            // Agregar al principio de la lista
            commentsList.insertBefore(newComment, commentsList.firstChild);
            
            // Limpiar el formulario
            form.reset();
        } else {
            alert('Error al guardar el comentario: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ha ocurrido un error al enviar el comentario');
    });
}

// Función para dar "Me gusta"
document.addEventListener('DOMContentLoaded', function() {
    // Agregar evento click a todos los botones de me gusta
    const likeButtons = document.querySelectorAll('.like-btn');
    likeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const publiID = this.getAttribute('data-publi');
            darMeGusta(publiID, this);
        });
    });
});

function darMeGusta(publiID, button) {
    // Crear FormData para enviar
    const formData = new FormData();
    formData.append('publiID', publiID);
    
    // Enviar con fetch
    fetch('../backend/reaccionar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar contador de likes
            const likesCounter = button.querySelector('.likes-counter');
            likesCounter.textContent = `(${data.likes})`;
            
            // Opcional: Cambiar estilo del botón si ya dio like
            if (data.liked) {
                button.classList.add('liked');
            } else {
                button.classList.remove('liked');
            }
        } else {
            alert('Error al procesar me gusta: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ha ocurrido un error al dar me gusta');
    });
}