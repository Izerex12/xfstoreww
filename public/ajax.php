<?php
// ajax.php
header('Content-Type: application/json');

// Подключаем ваш config.php
require_once 'config.php';

// Обработка GET-запроса (получение всех записей)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM feedback ORDER BY created_at DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Ошибка получения данных: ' . $e->getMessage()]);
    }
    exit;
}

// Обработка POST-запроса (сохранение новой записи)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Валидация
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'error' => 'Все поля обязательны']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Некорректный email']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO feedback (name, email, message) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $message]);
        
        echo json_encode(['success' => true, 'message' => 'Данные успешно сохранены']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Ошибка БД: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['error' => 'Недопустимый метод запроса']);
?>