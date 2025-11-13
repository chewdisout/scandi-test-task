<?php

declare(strict_types=1);

namespace App\GraphQL\Schema;

use App\GraphQL\Type\MutationType;
use App\GraphQL\Type\QueryType;
use GraphQL\Type\Schema;
use PDO;

class SchemaFactory
{
    public function __construct(private PDO $pdo)
    {
    }

    public function create(): Schema
    {
        return new Schema([
            'query' => new QueryType(),
            'mutation' => new MutationType(),
        ]);
    }

    public function getContext(): array
    {
        return ['pdo' => $this->pdo];
    }
}
