<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

class MySQLGalleryRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * @return string[]
     */
    public function findByProductId(string $productId): array
    {
        $sql = 'SELECT image_url FROM galleries WHERE product_id = :product_id ORDER BY sort_order ASC, id ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $productId]);

        return array_column($stmt->fetchAll(), 'image_url');
    }
}
