/* Добавление фильтрации списка при вводе текста */
document.getElementById('searchInput').addEventListener('input', function() {
    const filter = this.value.toLowerCase(); // Получаем текст из поля поиска
    const items = document.querySelectorAll('#dataList .game-item'); // Получаем все элементы списка

    items.forEach(function(item) {
        const text = item.querySelector('img').alt.toLowerCase(); // Получаем название игры из alt атрибута картинки
        if (text.indexOf(filter) > -1) {
            item.style.display = ""; // Показываем элемент, если он соответствует фильтру
        } else {
            item.style.display = "none"; // Прячем элемент, если не соответствует фильтру
        }
    });
});

