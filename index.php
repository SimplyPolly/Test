<?php 

include 'setup.php';
include 'tree.php';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=structure", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $tree = new Tree($pdo);

    $tree->addNode(1,'Уровень 1');
    $tree->addNode(2,'Потомок 1.1', parentId: 1); 
    $tree->addNode(3,'Потомок 1.2', parentId: 1);
    $tree->addNode(4,'Потомок 1.2.1', parentId: 3); 
    $tree->addNode(5,'Уровень 2');
    
    $treeStructure = $tree->getChildren();
    echo "<h2>Структура дерева:</h2>";
    echo "<pre>" . json_encode($treeStructure, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";

    echo "<h2>Плоский список:</h2>";
    $flatList = $tree->getFlatList();
    foreach ($flatList as $item) {
        echo $item . "<br>";
    }
} catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
}
