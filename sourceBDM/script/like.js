function toggleLike(publiID) {
    fetch('../backend/like_publicacion.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'publiID=' + publiID
    })
    .then(response => response.json())
    .then(data => {
        const likeBtn = document.getElementById('like-btn-' + publiID);
        const likeCount = document.getElementById('like-count-' + publiID);
        
        if (data.success) {
            if (data.liked) {
                likeBtn.innerHTML = '❤️ <span id="like-count-' + publiID + '">' + data.count + '</span>';
                likeBtn.classList.add('liked');
            } else {
                likeBtn.innerHTML = '🤍 <span id="like-count-' + publiID + '">' + data.count + '</span>';
                likeBtn.classList.remove('liked');
            }
        }
    })
    .catch(error => console.error('Error:', error));
}