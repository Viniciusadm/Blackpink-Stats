const videos = document.querySelector('#videos');
const ordenar = document.querySelector('#ordenar');

ordenar.addEventListener('change', function () {
    const ordenado = Array.from(videos.children).sort((a, b) => {
        if (ordenar.value === 'media' || ordenar.value === 'views') {
            return b.dataset[this.value] - a.dataset[this.value];
        }

        return a.dataset[this.value] - b.dataset[this.value];
    });

    videos.innerHTML = '';
    ordenado.forEach(video => videos.appendChild(video));
});