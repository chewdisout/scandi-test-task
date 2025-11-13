<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Attribute\AttributeItem;
use App\Domain\Attribute\AttributeSet;
use App\Domain\Attribute\TextAttributeSet;
use App\Domain\Attribute\SwatchAttributeSet;
use PDO;

class MySQLAttributeRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * @return AttributeSet[]
     */
    public function findByProductId(string $productId): array
    {
        $sql = 'SELECT * FROM attribute_sets WHERE product_id = :pid';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':pid' => $productId]);
        $sets = $stmt->fetchAll();

        $result = [];

        foreach ($sets as $setRow) {
            $itemsStmt = $this->pdo->prepare(
                'SELECT * FROM attribute_items WHERE attribute_set_id = :sid ORDER BY id ASC'
            );
            $itemsStmt->execute([':sid' => $setRow['id']]);
            $itemRows = $itemsStmt->fetchAll();

            $items = array_map(
                fn (array $ir) => new AttributeItem(
                    (int) $ir['id'],
                    $ir['display_value'],
                    $ir['value'],
                    $ir['slug']
                ),
                $itemRows
            );

            $result[] = $this->createSet(
                (int) $setRow['id'],
                $setRow['name'],
                $setRow['type'],
                $items
            );
        }

        return $result;
    }

    private function createSet(int $id, string $name, string $type, array $items): AttributeSet
    {
        return match ($type) {
            'swatch' => new SwatchAttributeSet($id, $name, $items),
            default  => new TextAttributeSet($id, $name, $items),
        };
    }
}
