<?php
// script.php
require_once 'config.php';

function getAllGames($pdo) {
    $stmt = $pdo->query("SELECT * FROM games ORDER BY id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getGameById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM games WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function saveOrder($pdo, $game_id, $email, $payment_method) {
    $stmt = $pdo->prepare("INSERT INTO orders (game_id, email, payment_method) VALUES (?, ?, ?)");
    return $stmt->execute([$game_id, $email, $payment_method]);
}
?>