<?php

class Tree
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function addNode($id, string $name, $parentId = null): void
    {
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("INSERT INTO trees_item (id, name, parent_id) VALUES (:id, :name, :parent_id) ON CONFLICT (id) DO NOTHING");
            $stmt->execute([':id' => $id, ':name' => $name, ':parent_id' => $parentId]);
            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
    public function getChildren($parentId = null): array
    {
        return $this->getNodeTree($parentId);
    }

    public function getNodeTree($parentId = null): array
    {
        $tree = [];
        $stmt = $this->pdo->prepare("SELECT * FROM trees_item WHERE parent_id " . ($parentId === null ? "IS NULL" : "= :parent_id"));
        if ($parentId !== null) {
            $stmt->bindParam(':parent_id', $parentId);
        }
        $stmt->execute();
        $nodes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($nodes as $node) {
            $children = $this->getNodeTree($node['id']);
            $tree[] = [
                'id' => $node['id'],
                'name' => $node['name'],
                'children' => $children
            ];
        }
        return $tree;
    }

    public function getFlatList($parentId = null): array
    {
        return $this->getNodeFlatList($parentId);
    }

    private function getNodeFlatList($parentId = null, string $prefix = ''): array
    {
        $list = [];
        $stmt = $this->pdo->prepare("SELECT * FROM trees_item WHERE parent_id " . ($parentId === null ? "IS NULL" : "= :parent_id"));
        if ($parentId !== null) {
            $stmt->bindParam(':parent_id', $parentId);
        }
        $stmt->execute();
        $nodes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($nodes as $node) {
            $list[] = $prefix . $node['name'];
            $list = array_merge($list, $this->getNodeFlatList($node['id'], $prefix . '-- '));
        }
        return $list;
    }

    public function deleteNode($id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM trees_item WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
}
