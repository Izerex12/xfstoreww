<?php
// buy.php
require_once 'script.php';

$game_id = isset($_GET['game_id']) ? (int)$_GET['game_id'] : 0;
$game = getGameById($pdo, $game_id);

if (!$game) {
    die('Игра не найдена.');
}

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $payment_method = $_POST['payment_method'];

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Некорректный email.';
    } elseif (empty($payment_method)) {
        $error = 'Выберите способ оплаты.';
    } else {
        if (saveOrder($pdo, $game_id, $email, $payment_method)) {
            $success = true;
        } else {
            $error = 'Ошибка при сохранении заказа.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XF Store - Купить</title>
    <link rel="stylesheet" href="buy.css">
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

<main>
    <section class="buy-section">
        <div class="container">
            <h2>Оформить покупку</h2>
            <div class="purchase-form">
                <?php if ($success): ?>
                    <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px;">
                        <strong>Успешно!</strong> Спасибо за покупку. Мы свяжемся с вами по email.
                    </div>
                <?php elseif ($error): ?>
                    <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;">
                        <strong>Ошибка:</strong> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="buy.php?game_id=<?= $game_id ?>" method="POST" id="purchaseForm">
                    <div class="form-group">
                        <label for="game-name">Название игры</label>
                        <input type="text" id="game-name" value="<?= htmlspecialchars($game['name']) ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Введите ваш email" required>
                    </div>
                    <div class="form-group">
                        <label for="payment-method">Способ оплаты</label>
                        <select id="payment-method" name="payment_method" required>
                            <option value="card">Карта</option>
                            <option value="stp">СБП</option>
                            <option value="paypal">PayPal</option>
                        </select>
                    </div>
                    <button type="submit" class="btn buy-btn">Купить</button>
                </form>
            </div>
        </div>
    </section>
</main>

<footer>
    <div class="container">
        <p>&copy; 2026 XF Store. Все права защищены.</p>
    </div>
</footer>

<script>
    document.getElementById('purchaseForm').addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        const regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (!regex.test(email)) {
            alert('Введите корректный email.');
            e.preventDefault();
        }
    });
</script>
</body>
</html>