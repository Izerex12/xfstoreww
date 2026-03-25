<?php
// search.php
require_once 'script.php';

$games = getAllGames($pdo);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XF Store - Поиск</title>
    <link rel="stylesheet" href="search.css">
    <link rel="icon" href="favicon (1).ico" type="image/x-icon">
</head>
<body>
<header>
    <div class="container">
        <h1>XF Store</h1>
        <nav>
            <ul>
                <li><a href="index.html">Главная</a></li>
                <li><a href="search.php">Поиск</a></li>
                <li><a href="#">Профиль</a></li>
                <li><a href="#">Корзина (0)</a></li>
            </ul>
        </nav>
    </div>
</header>

<input type="text" class="search-input" id="searchInput" placeholder="Название игры">

<main>
    <section class="hero">
        <div class="container">
            <h2>Все игры</h2>
            <div class="gallery" id="dataList">
                <?php foreach ($games as $game): ?>
                    <div class="game-item" data-game-id="<?= $game['id'] ?>">
                        <img src="<?= htmlspecialchars($game['image']) ?>" alt="<?= htmlspecialchars($game['name']) ?>">
                        <a href="#" class="btn" onclick="openModal(<?= $game['id'] ?>); return false;">Подробнее</a>
                    </div>

                    <!-- Модальное окно для каждой игры -->
                    <div class="game-modal" id="gameModal<?= $game['id'] ?>">
                        <div class="modal-content">
                            <span class="close-btn" onclick="closeModal(<?= $game['id'] ?>)">&times;</span>
                            <img src="<?= htmlspecialchars($game['image']) ?>" alt="Game Image" class="game-image">
                            <h3><?= htmlspecialchars($game['name']) ?></h3>
                            <p><?= nl2br(htmlspecialchars($game['description'])) ?></p>
                            <p><strong>Цена:</strong> <?= number_format($game['price'], 2, '.', ' ') ?> руб</p>
                            <p><strong>Системные требования:</strong></p>
                            <ul>
                                <?php
                                $reqs = explode(';', $game['system_requirements']);
                                foreach ($reqs as $req) {
                                    echo '<li>' . htmlspecialchars(trim($req)) . '</li>';
                                }
                                ?>
                            </ul>
                            <a href="buy.php?game_id=<?= $game['id'] ?>" class="btn buy-btn">Купить</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<footer>
    <div class="container">
        <p>&copy; 2026 XF Store. Все права защищены.</p>
    </div>
</footer>

<script src="search.js"></script>
<script>
    function openModal(gameId) {
        document.getElementById('gameModal' + gameId).style.display = "block";
    }
    function closeModal(gameId) {
        document.getElementById('gameModal' + gameId).style.display = "none";
    }
    window.onclick = function(event) {
        if (event.target.classList.contains('game-modal')) {
            event.target.style.display = "none";
        }
    }
</script>
</body>
</html>