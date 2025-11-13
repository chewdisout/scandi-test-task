<?php

declare(strict_types=1);

namespace App\Database\Seed;

use App\Database\Connection;
use Dotenv\Dotenv;
use PDO;

require __DIR__ . '/../../../vendor/autoload.php';

// Load env
$root = dirname(__DIR__, 3); // backend/
if (file_exists($root . '/.env')) {
    Dotenv::createImmutable($root)->load();
}

$pdo = Connection::getInstance();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$jsonPath = $root . '/data.json';
if (!file_exists($jsonPath)) {
    echo "data.json not found at {$jsonPath}\n";
    exit(1);
}

$payload = json_decode(file_get_contents($jsonPath), true, 512, JSON_THROW_ON_ERROR);

$data = $payload['data'] ?? $payload;

if (
    !isset($data['categories'], $data['products']) ||
    !is_array($data['categories']) ||
    !is_array($data['products'])
) {
    echo "Unexpected data.json structure\n";
    exit(1);
}

$categoriesData = $data['categories'];
$productsData   = $data['products'];

echo "Seeding database...\n";

$pdo->exec('SET FOREIGN_KEY_CHECKS=0');
$pdo->exec('TRUNCATE TABLE order_items');
$pdo->exec('TRUNCATE TABLE orders');
$pdo->exec('TRUNCATE TABLE attribute_items');
$pdo->exec('TRUNCATE TABLE attribute_sets');
$pdo->exec('TRUNCATE TABLE galleries');
$pdo->exec('TRUNCATE TABLE prices');
$pdo->exec('TRUNCATE TABLE products');
$pdo->exec('TRUNCATE TABLE categories');
$pdo->exec('TRUNCATE TABLE currencies');
$pdo->exec('SET FOREIGN_KEY_CHECKS=1');

// ====== CATEGORIES ======
$categoryIds = []; // name => id
$insertCategory = $pdo->prepare('INSERT INTO categories (name) VALUES (:name)');

foreach ($categoriesData as $cat) {
    $name = $cat['name'];
    if (isset($categoryIds[$name])) {
        continue;
    }
    $insertCategory->execute([':name' => $name]);
    $categoryIds[$name] = (int) $pdo->lastInsertId();
}

echo "Inserted " . count($categoryIds) . " categories\n";

// ====== CURRENCIES ======
$currencyIds = []; // label => id
$insertCurrency = $pdo->prepare(
    'INSERT INTO currencies (label, symbol) VALUES (:label, :symbol)'
);

// ====== PREPARED STATEMENTS ======
$insertProduct = $pdo->prepare(
    'INSERT INTO products (id, name, in_stock, description, category_id, brand)
     VALUES (:id, :name, :in_stock, :description, :category_id, :brand)'
);

$insertPrice = $pdo->prepare(
    'INSERT INTO prices (product_id, currency_id, amount)
     VALUES (:product_id, :currency_id, :amount)'
);

$insertGallery = $pdo->prepare(
    'INSERT INTO galleries (product_id, image_url, sort_order)
     VALUES (:product_id, :image_url, :sort_order)'
);

$insertAttrSet = $pdo->prepare(
    'INSERT INTO attribute_sets (product_id, name, type)
     VALUES (:product_id, :name, :type)'
);

$insertAttrItem = $pdo->prepare(
    'INSERT INTO attribute_items (attribute_set_id, display_value, value, slug)
     VALUES (:set_id, :display_value, :value, :slug)'
);

// ====== PRODUCTS LOOP ======
foreach ($productsData as $p) {
    $productId = $p['id'];

    $catName = $p['category'] ?? 'all';
    if (!isset($categoryIds[$catName])) {
        $insertCategory->execute([':name' => $catName]);
        $categoryIds[$catName] = (int) $pdo->lastInsertId();
    }
    $categoryId = $categoryIds[$catName];

    // product
    $insertProduct->execute([
        ':id'          => $productId,
        ':name'        => $p['name'],
        ':in_stock'    => $p['inStock'] ? 1 : 0,
        ':description' => $p['description'] ?? '',
        ':category_id' => $categoryId,
        ':brand'       => $p['brand'] ?? '',
    ]);

    // gallery
    if (!empty($p['gallery']) && is_array($p['gallery'])) {
        foreach ($p['gallery'] as $idx => $url) {
            $insertGallery->execute([
                ':product_id' => $productId,
                ':image_url'  => $url,
                ':sort_order' => $idx,
            ]);
        }
    }

    // prices + currencies
    if (!empty($p['prices']) && is_array($p['prices'])) {
        foreach ($p['prices'] as $price) {
            $curr   = $price['currency'];
            $label  = $curr['label'];
            $symbol = $curr['symbol'];

            if (!isset($currencyIds[$label])) {
                $insertCurrency->execute([
                    ':label'  => $label,
                    ':symbol' => $symbol,
                ]);
                $currencyIds[$label] = (int) $pdo->lastInsertId();
            }

            $insertPrice->execute([
                ':product_id'  => $productId,
                ':currency_id' => $currencyIds[$label],
                ':amount'      => $price['amount'],
            ]);
        }
    }

    // attributes
    if (!empty($p['attributes']) && is_array($p['attributes'])) {
        foreach ($p['attributes'] as $attr) {
            $insertAttrSet->execute([
                ':product_id' => $productId,
                ':name'       => $attr['name'],
                ':type'       => $attr['type'],
            ]);
            $setId = (int) $pdo->lastInsertId();

            foreach ($attr['items'] as $item) {
                $display = $item['displayValue'];
                $value   = $item['value'];
                $slug    = strtolower(preg_replace('/[^a-z0-9]+/i', '-', (string)($item['id'] ?? $value)));

                $insertAttrItem->execute([
                    ':set_id'        => $setId,
                    ':display_value' => $display,
                    ':value'         => $value,
                    ':slug'          => $slug,
                ]);
            }
        }
    }
}

echo "Seed complete. Inserted " . count($productsData) . " products, "
    . count($currencyIds) . " currencies.\n";
