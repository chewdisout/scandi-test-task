<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Category\Category;
use PDO;

class MySQLCategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT id, name FROM categories ORDER BY id ASC');
        $rows = $stmt->fetchAll();

        return array_map(
            fn (array $row) => new Category((int) $row['id'], $row['name']),
            $rows
        );
    }
}
