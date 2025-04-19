// Mostrar u ocultar los comentarios
function mostrarComentarios(publiID) {
    const comentariosSection = document.getElementById('comentarios-' + publiID);
    comentariosSection.style.display = (comentariosSection.style.display === 'none') ? 'block' : 'none';
}

// Enviar comentario
function enviarComentario(event, publiID) {
    event.preventDefault();

    const form = event.target;
    const comentarioText = form.comentario.value;

    const formData = new FormData();
    formData.append('publiID', publiID);
    formData.append('comentario', comentarioText);

    fetch('../backend/guardar_comentario.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const comentariosSection = document.getElementById('comentarios-' + publiID);
            let commentsList = comentariosSection.querySelector('.comments-list');

            if (!commentsList) {
                commentsList = document.createElement('div');
                commentsList.className = 'comments-list';

                const noComments = comentariosSection.querySelector('.no-comments');
                if (noComments) comentariosSection.removeChild(noComments);

                comentariosSection.insertBefore(commentsList, comentariosSection.querySelector('.comment-form'));
            }

            const newComment = document.createElement('div');
            newComment.className = 'comment';
            newComment.innerHTML = `
                <span class="comment-username">@${data.userNick}:</span>
                <span class="comment-text">${data.comentario}</span>
            `;

            commentsList.insertBefore(newComment, commentsList.firstChild);
            form.reset();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al enviar el comentario');
    });
}
