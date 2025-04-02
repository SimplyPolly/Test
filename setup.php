<?php
$host = "localhost";
$user = "postgres";
$password = "1234";
$pdo;

try {
    $pdo = new PDO("pgsql:host=$host", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dbName = "structure";

    try {
    $pdo->exec("CREATE DATABASE \"$dbName\"");
    echo "База данных '$dbName' успешно создана.<br>";
    } catch (PDOException $e) {
        if ($e->getCode() == '42P04') { 
            echo "База данных '$dbName' уже существует.<br>";
        } else {
            throw $e; 
        }
    }

    $pdo = new PDO("pgsql:host=$host;dbname=$dbName", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = '
    CREATE TABLE IF NOT EXISTS trees_item (
        id SERIAL PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        parent_id INT DEFAULT NULL,
        FOREIGN KEY (parent_id) REFERENCES trees_item(id) ON DELETE CASCADE 
    )';

    $pdo->exec($sql);
    echo "База данных и таблица успешно созданы.";
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
}