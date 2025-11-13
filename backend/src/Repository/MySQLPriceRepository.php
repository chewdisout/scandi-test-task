<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Price\Currency;
use App\Domain\Price\Price;
use PDO;

class MySQLPriceRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * @return Price[]
     */
    public function findByProductId(string $productId): array
    {
        $sql = 'SELECT p.amount, c.id as currency_id, c.label, c.symbol
                FROM prices p
                JOIN currencies c ON p.currency_id = c.id
                WHERE p.product_id = :product_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $productId]);
        $rows = $stmt->fetchAll();

        $prices = [];
        foreach ($rows as $row) {
            $currency = new Currency(
                (int) $row['currency_id'],
                $row['label'],
                $row['symbol'],
            );
            $prices[] = new Price($currency, (float) $row['amount']);
        }

        return $prices;
    }
}
