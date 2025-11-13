<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Product\Product;
use App\Domain\Product\SimpleProduct;
use PDO;

class MySQLProductRepository implements ProductRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function findByCategoryName(?string $categoryName): array
    {
        if ($categoryName === null || $categoryName === '' || $categoryName === 'all') {
            $stmt = $this->pdo->query('SELECT * FROM products');
        } else {
            $sql = 'SELECT p.* FROM products p
                    JOIN categories c ON p.category_id = c.id
                    WHERE c.name = :name';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':name' => $categoryName]);
        }

        $rows = $stmt->fetchAll();

        return array_map(fn (array $row) => $this->mapRowToProduct($row), $rows);
    }

    private function mapRowToProduct(array $row): Product
    {
        return new SimpleProduct(
            $row['id'],
            $row['name'],
            (bool) $row['in_stock'],
            $row['description'] ?? '',
            (int) $row['category_id'],
            $row['brand'] ?? ''
        );
    }

    public function findById(string $id): ?Product
    {
        $stmt = $this->pdo->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->mapRowToProduct($row);
    }
}
