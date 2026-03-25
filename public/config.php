<?php
$host = 'MySQL-8.0';      // или 'MySQL-8.0', но Adminer показал root@localhost
$dbname = 'xf_store';
$username = 'root';
$password = '123';         // пароль, который вы задали

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}
?>