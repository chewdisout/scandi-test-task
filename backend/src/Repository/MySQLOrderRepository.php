<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Order\Order;
use App\Domain\Order\OrderItem;
use App\Domain\Order\SelectedAttribute;
use App\Domain\Product\Product;
use App\Domain\Product\SimpleProduct;
use PDO;
use RuntimeException;

class MySQLOrderRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @param array{
     *   items: array<int, array{
     *     productId: string,
     *     quantity: int,
     *     selectedAttributes: array<int, array{name: string, value: string}>
     *   }>
     * } $input
     */
    public function createOrder(array $input): Order
    {
        if (empty($input['items'])) {
            throw new RuntimeException('Order must contain at least one item.');
        }

        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare('INSERT INTO orders () VALUES ()');
            $stmt->execute();
            $orderId = (int) $this->pdo->lastInsertId();

            $items = [];

            foreach ($input['items'] as $itemInput) {
                $product = $this->getProductById($itemInput['productId']);
                if (!$product) {
                    throw new RuntimeException('Invalid product: ' . $itemInput['productId']);
                }

                $quantity = max(1, (int) $itemInput['quantity']);

                $selectedAttributes = array_map(
                    fn (array $sa) => new SelectedAttribute($sa['name'], $sa['value']),
                    $itemInput['selectedAttributes'] ?? []
                );

                $json = json_encode(
                    array_map(fn (SelectedAttribute $sa) => $sa->toArray(), $selectedAttributes),
                    JSON_THROW_ON_ERROR
                );

                $insert = $this->pdo->prepare(
                    'INSERT INTO order_items (order_id, product_id, quantity, selected_attributes)
                     VALUES (:order_id, :product_id, :quantity, :selected_attributes)'
                );
                $insert->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $product->getId(),
                    ':quantity' => $quantity,
                    ':selected_attributes' => $json,
                ]);

                $orderItemId = (int) $this->pdo->lastInsertId();

                $items[] = new OrderItem(
                    $orderItemId,
                    $product,
                    $quantity,
                    $selectedAttributes
                );
            }

            $this->pdo->commit();

            $createdAt = $this->fetchOrderCreatedAt($orderId);

            return new Order($orderId, $createdAt, $items);
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function getProductById(string $id): ?Product
    {
        $stmt = $this->pdo->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new SimpleProduct(
            $row['id'],
            $row['name'],
            (bool) $row['in_stock'],
            $row['description'] ?? '',
            (int) $row['category_id'],
            $row['brand'] ?? ''
        );
    }

    private function fetchOrderCreatedAt(int $orderId): string
    {
        $stmt = $this->pdo->prepare('SELECT created_at FROM orders WHERE id = :id');
        $stmt->execute([':id' => $orderId]);
        $row = $stmt->fetch();

        return $row['created_at'] ?? '';
    }
}
