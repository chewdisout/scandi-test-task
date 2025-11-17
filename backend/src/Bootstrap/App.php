<?php

declare(strict_types=1);

namespace App\Bootstrap;

use App\Database\Connection;
use App\Http\Controller\GraphQLController;
use Dotenv\Dotenv;

class App
{
    public function run(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        $dotenvPath = __DIR__ . '/../../';
        if (file_exists($dotenvPath . '.env')) {
            Dotenv::createImmutable($dotenvPath)->load();
        }

        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if ($path === '/graphql') {
            $controller = new GraphQLController(
                Connection::getInstance()
            );
            $controller->handle();
            return;
        }

        if ($path === '/') {
            header('Content-Type: application/json');
            echo json_encode([
                'message' => 'Backend up. POST /graphql with a GraphQL query.'
            ]);
            return;
        }

        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Not found']);
    }
}
