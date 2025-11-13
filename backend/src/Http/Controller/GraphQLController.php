<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\GraphQL\Schema\SchemaFactory;
use GraphQL\GraphQL;

class GraphQLController
{
    public function __construct(
        private \PDO $pdo
    ) {}

    public function handle(): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $query = $input['query'] ?? '';
        $variables = $input['variables'] ?? null;

        $schemaFactory = new SchemaFactory($this->pdo);
        $schema = $schemaFactory->create();

        $result = GraphQL::executeQuery(
            $schema,
            $query,
            null,
            $schemaFactory->getContext(),
            $variables
        );

        $output = $result->toArray();

        header('Content-Type: application/json');
        echo json_encode($output);
    }
}
